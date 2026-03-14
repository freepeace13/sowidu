import { Role } from "@domain/teams/permissions/Role"
import {
  GetTeamRolePermissionsData,
  RolePermissionsRepository,
} from "@domain/teams/permissions/RolePermissionsRepository"
import { TeamRole as TeamRoleSchema } from "@infrastructure/schema/TeamRole"

import {
  GetTeamRolesUri,
  GetTeamRolePermissionsUri,
  UpdateTeamRolesUri,
  CreateTeamRoleUri,
} from "@infrastructure/api/urls"
import { request } from "@infrastructure/api/request"
import { teamRoleTransformer } from "@infrastructure/transformers/TeamRoleTransformer"

export const teamRoleRepository: RolePermissionsRepository = {
  async getRoles(teamId) {
    return await request<{ data: TeamRoleSchema[] }, Role[]>({
      url: GetTeamRolesUri.replace({ teamId }),
      method: "GET",
      transformResponse: ({ data }) => teamRoleTransformer.collection(data.data),
    })
  },

  async updateRolePermissions(teamId, params) {
    return await request<{ data: TeamRoleSchema }, Role>({
      url: UpdateTeamRolesUri.replace({ teamId, roleId: params.roleId }),
      method: "POST",
      body: {
        _method: "PATCH",
        permissions: params.permissions,
      },
    })
  },

  async createRole(teamId, params) {
    return await request<{ data: TeamRoleSchema }, Role>({
      url: CreateTeamRoleUri.replace({ teamId }),
      method: "POST",
      body: {
        name: params.roleName,
      },
      transformResponse: ({ data }) => teamRoleTransformer.transform(data.data),
    })
  },

  async showRole(teamId: number, params: GetTeamRolePermissionsData) {
    return await request<{ data: TeamRoleSchema }, Role>({
      url: GetTeamRolePermissionsUri.replace({ teamId, roleId: params.roleId }),
      method: "GET",
      transformResponse: ({ data }) => teamRoleTransformer.transform(data.data),
    })
  },
}
