import { Team } from "@domain/teams/team/Team"
import { TeamRepository } from "@domain/teams/team/TeamRepository"

import { Team as TeamSchema } from "@infrastructure/schema/Team"
import {
  GetTeamsUri,
  CreateTeamUri,
  UpdateTeamInfoUri,
  UpdateTeamAvatarUri,
  GetTeamInfoUri,
} from "@infrastructure/api/urls"
import { teamTransformer } from "@infrastructure/transformers/TeamTransformer"
import { request } from "@infrastructure/api/request"
import { rejectNullableValues } from "@infrastructure/utils/helpers"

export const teamRepository: TeamRepository = {
  async getTeams() {
    return await request<{ data: TeamSchema[] }, Team[]>({
      url: GetTeamsUri.replace({}),
      method: "GET",
      transformResponse: ({ data }) => teamTransformer.collection(data.data),
    })
  },

  async getTeamInfo(teamId) {
    return await request<{ data: TeamSchema }, Team>({
      url: GetTeamInfoUri.replace({ teamId }),
      method: "GET",
      transformResponse: ({ data }) => teamTransformer.transform(data.data),
    })
  },

  async updateInfo(teamId, params) {
    return await request<{ data: TeamSchema }, Team>({
      url: UpdateTeamInfoUri.replace({ teamId }),
      method: "GET",
      body: {
        _method: "PATCH",
        name: params.name,
        institutionType: params.institutionType,
        legalForm: params.legalForm,
      },
      transformResponse: ({ data }) => teamTransformer.transform(data.data),
    })
  },

  async updateAvatar(teamId, params) {
    const fd = new FormData()
    fd.append("_method", "PATCH")
    fd.append("avatar", params.image as any)

    return await request<{ data: TeamSchema }, string>({
      url: UpdateTeamAvatarUri.replace({ teamId }),
      method: "POST",
      body: fd,
      headers: {
        "Content-Type": "multipart/form-data",
      },
      transformResponse: ({ data }) => data.data.photo,
    })
  },

  async createTeam(params) {
    return await request<{ data: TeamSchema }, Team>({
      url: CreateTeamUri.replace({}),
      method: "POST",
      body: rejectNullableValues({
        name: params.name,
        institutionTypeId: params.institutionTypeId,
        legalFormId: params.legalFormId,
      }),
    })
  },
}
