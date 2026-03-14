import { teamMemberService, teamService, teamRoleService } from "@application/main"
import { Permission } from "@domain/teams/permissions/Permission"

import { sharedApi } from "@presentation/features/shared/api"
import { RootState } from "@presentation/app/store"
import { CurrencyCode, Nullable } from "@domain/shared/types"
import { Team } from "@domain/teams/team/Team"
import type {
  CreateTeamData,
  GetTeamsData,
  UpdateTeamAvatarData,
  UpdateTeamInfoData,
} from "@domain/teams/team/TeamRepository"
import { Role } from "@domain/teams/permissions/Role"
import { Member } from "@domain/teams/members/Member"
import { createSelector } from "@reduxjs/toolkit"
import { SearchNewTeamMembersData } from "@domain/teams/members/MemberRepository"

const teamsApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    getTeams: build.query<Team[], GetTeamsData | undefined>({
      queryFn: (args) => {
        return teamService
          .getJoinedTeams(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    createTeam: build.mutation<Team, CreateTeamData>({
      queryFn: (args) => {
        return teamService
          .createTeam(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    searchUsersForInvitation: build.query<any[], SearchNewTeamMembersData & { teamId: number }>({
      queryFn: (args) => {
        return teamMemberService
          .searchMembersForInvitation(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    getRoles: build.query<Role[], { teamId: number }>({
      queryFn: ({ teamId }) => {
        return teamRoleService
          .getTeamRoles({ teamId })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      providesTags: ["Roles"],
      keepUnusedDataFor: 60 * 60 * 2, // Will keep the data for 2 hours
    }),

    createRole: build.mutation<Role, { teamId: number; roleName: string }>({
      queryFn: ({ teamId, roleName }) => {
        return teamRoleService
          .createTeamRole({ teamId, roleName })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      invalidatesTags: ["Roles"],
    }),

    getRoleDetails: build.query<Role, { teamId: number; roleId: number }>({
      queryFn: ({ teamId, roleId }) => {
        return teamRoleService
          .getTeamRolePermissions({ teamId, roleId })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    updateRolePermissions: build.mutation<
      Role,
      { teamId: number; roleId: number; permissions: number[] }
    >({
      queryFn: ({ teamId, roleId, permissions }) => {
        return teamRoleService
          .updateTeamRolePermissions({ teamId, roleId, permissions })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted({ teamId, roleId, permissions }, { dispatch, queryFulfilled }) {
        let prevPermissions: Permission[] | undefined = []
        try {
          dispatch(
            teamsApi.util.updateQueryData(
              "getRoleDetails" as const,
              { teamId, roleId },
              (draft) => {
                prevPermissions = draft.permissions
                Object.assign(draft, {
                  permissions: (draft.permissions || []).map((item) => ({
                    ...item,
                    hasDirectPermission: permissions.includes(item.id),
                  })),
                })
              }
            )
          )
          await queryFulfilled
        } catch {
          dispatch(
            teamsApi.util.updateQueryData(
              "getRoleDetails" as const,
              { teamId, roleId },
              (draft) => {
                Object.assign(draft, { permissions: prevPermissions })
              }
            )
          )
        }
      },
    }),

    sendInvitation: build.mutation<
      boolean,
      { teamId: number; email: string; role: string; message?: string }
    >({
      queryFn: ({ teamId, role, email, message }) => {
        return teamMemberService
          .sendTeamInvitation({ teamId, email, role, message })
          .then(() => ({ data: true }))
          .catch((error) => ({ error }))
      },
    }),

    updateTeamAvatar: build.mutation<string, UpdateTeamAvatarData & { teamId: number }>({
      queryFn: ({ teamId, image }) => {
        return teamService
          .changeTeamAvatar({ teamId, image })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted({ teamId }, { dispatch, queryFulfilled }) {
        const { data: photoURL } = await queryFulfilled

        dispatch(
          teamsApi.util.updateQueryData("getTeams" as const, undefined, (draft) => {
            const index = draft.findIndex((i) => i.id === teamId)
            if (index !== -1) {
              draft.splice(index, 1, { ...draft[index], photoURL })
            }
          })
        )
      },
    }),

    updateTeamInfo: build.mutation<Team, UpdateTeamInfoData & { teamId: number }>({
      queryFn: ({ institutionType, legalForm, ...args }) => {
        return teamService
          .updateTeamInfo({
            ...args,
            institutionType: institutionType,
            legalForm: legalForm,
          })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted({ teamId }, { dispatch, queryFulfilled }) {
        const { data: teamInfo } = await queryFulfilled
        dispatch(
          teamsApi.util.updateQueryData("getTeams" as const, undefined, (draft) => {
            const index = draft.findIndex((i) => i.id === teamId)
            if (index !== -1) {
              const updatedInfo = {
                name: teamInfo.name,
                institutionType: teamInfo.institutionType,
                legalForm: teamInfo.legalForm,
              }

              draft.splice(index, 1, { ...draft[index], ...updatedInfo })
            }
          })
        )
      },
    }),

    getMembers: build.query<Member[], { teamId: number }>({
      queryFn: ({ teamId }) => {
        return teamMemberService
          .getTeamMembers({ teamId })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    getMembersInfo: build.query<
      Member,
      {
        teamId: number
        memberId: number
      }
    >({
      queryFn: ({ teamId, memberId }) => {
        return teamMemberService
          .getTeamMemberInfo({ teamId, memberId })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      providesTags: (member) => [{ type: "TeamMember", id: member?.memberId }],
    }),

    updateMembersInfo: build.mutation<
      Member,
      {
        teamId: number
        memberId: number
        roles?: string[]
        contactNumber?: any
        rates?: {
          currency: CurrencyCode
          rate: string
        }
      }
    >({
      queryFn: (args) => {
        return teamMemberService
          .updateTeamMemberInfo(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted(args, { dispatch, queryFulfilled }) {
        const { data } = await queryFulfilled
        dispatch(
          teamsApi.util.updateQueryData(
            "getMembersInfo",
            {
              teamId: args.teamId,
              memberId: args.memberId,
            },
            (draft) => {
              Object.assign(draft, {
                teamRoles: data.teamRoles,
                userInfo: data.userInfo,
                rates: data.rates,
              })
            }
          )
        )
      },
    }),
  }),
})

export const resetTeamsApiState = teamsApi.util.resetApiState
export const updateTeamsApiQueryData = teamsApi.util.updateQueryData

export const { getTeams, createTeam, getMembers, searchUsersForInvitation } = teamsApi.endpoints

export const {
  useGetTeamsQuery,
  useUpdateTeamAvatarMutation,
  useUpdateTeamInfoMutation,
  useGetRoleDetailsQuery,
  useGetRolesQuery,
  useSearchUsersForInvitationQuery,
  useCreateTeamMutation,
  useCreateRoleMutation,
  useUpdateRolePermissionsMutation,
  useSendInvitationMutation,
  useGetMembersInfoQuery,
  useUpdateMembersInfoMutation,
  useGetMembersQuery,
} = teamsApi

export const createGetTeamsSelector = createSelector(
  (state: RootState) => getTeams.select(undefined)(state),
  (response) => response
)

export const selectGetTeamsInitialized = createSelector(
  (state: RootState) => createGetTeamsSelector(state),
  (response) => !response.isUninitialized
)

export const selectAllTeams = createSelector(
  (state: RootState) => createGetTeamsSelector(state),
  (response) => response.data || []
)

export const selectTeamById = (teamId: Nullable<number>) =>
  createSelector(
    (state: RootState) => selectAllTeams(state),
    (teams) => teams.find((team) => teamId && team.id === teamId)
  )

export const createGetMembersSelector = createSelector(
  (state: RootState, teamId: number) => getMembers.select({ teamId })(state),
  (response) => response
)

export const selectMembers = createSelector(
  (state: RootState, teamId: number) => createGetMembersSelector(state, teamId),
  (response) => response.data || []
)
