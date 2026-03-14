import { MemberRepository } from "@domain/teams/members/MemberRepository"
import { TeamMember as TeamMemberSchema } from "@infrastructure/schema/TeamMember"
import { Member } from "@domain/teams/members/Member"

import {
  GetTeamMembersURi,
  SendTeamInvitationUri,
  SearchNewMembersUri,
  GetTeamMembersInfoUri,
  UpdateTeamMembersInfoUri,
} from "@infrastructure/api/urls"
import { teamMemberTransformer } from "@infrastructure/transformers/TeamMemberTransformer"
import { request } from "@infrastructure/api/request"

export const teamMemberRepository: MemberRepository = {
  async sendInvitation(teamId, params) {
    return await request<{ data: any }, any>({
      url: SendTeamInvitationUri.replace({ teamId }),
      method: "POST",
      body: {
        email: params.email,
        role: params.role,
        message: params.message,
      },
      transformResponse: ({ data }) => data.data,
    })
  },

  async getNewMembers(teamId, params) {
    return await request<{ data: any[] }, any>({
      url: SearchNewMembersUri.replace({ teamId }),
      method: "GET",
      params: {
        q: params.keyword,
        limit: params.limit,
      },
      transformResponse: ({ data }) => data.data,
    })
  },

  async getTeamMembers(teamId) {
    return await request<{ data: TeamMemberSchema[] }, Member[]>({
      url: GetTeamMembersURi.replace({ teamId }),
      method: "GET",
      transformResponse: ({ data }) => teamMemberTransformer.collection(data.data),
    })
  },

  async getMembersInfo(teamId, { memberId }) {
    return await request<{ data: TeamMemberSchema }, Member>({
      url: GetTeamMembersInfoUri.replace({ teamId, memberId }),
      method: "GET",
      transformResponse: ({ data }) => teamMemberTransformer.transform(data.data),
    })
  },

  async updateMembersInfo(teamId, params) {
    const { memberId, ...restParams } = params
    return await request<{ data: TeamMemberSchema }, Member>({
      url: UpdateTeamMembersInfoUri.replace({ teamId, memberId }),
      method: "POST",
      body: {
        _method: "PATCH",
        roles: restParams.roles,
        contactNumber: restParams.contactNumber,
        rates: restParams.rates,
      },
      transformResponse: ({ data }) => teamMemberTransformer.transform(data.data),
    })
  },
}
