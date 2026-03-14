import { Api as CoreApi, ApiCacher } from "core-module"

import { TeamInfo } from "../../types"

type SwitchTeamData = {
  teamId: number | null
}

export const createTeam = (params) => {
  return {
    id: params.id,
    name: params.name,
    photo: params.photo,
  } as TeamInfo
}

export const teamsApi = CoreApi.injectEndpoints({
  endpoints: (builder) => ({
    getTeams: builder.query<TeamInfo[], void>({
      query: () => ({
        url: "teams",
        method: "GET",
      }),
      providesTags: (result) => ApiCacher.providesList(result, "Teams"),
      transformResponse: (baseQueryReturnValue): TeamInfo[] => {
        return baseQueryReturnValue.map((value) => createTeam(value))
      },
    }),

    switchTeam: builder.mutation<void, Partial<SwitchTeamData>>({
      query: ({ teamId }) => ({
        url: `teams/${teamId}/switch`,
        method: "PATCH",
        data: { teamId },
      }),
      invalidatesTags: ["User"],
    }),
  }),
})

export const { getTeams, switchTeam } = teamsApi.endpoints

export const { useGetTeamsQuery, useSwitchTeamMutation } = teamsApi

export default {
  getTeams,
  switchTeam,
  useGetTeamsQuery,
  useSwitchTeamMutation,
}
