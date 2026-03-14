import { Role } from "./Role"

export type CreateTeamRoleData = {
  roleName: string
}

export type GetTeamRolePermissionsData = {
  roleId: number
}

export type UpdateTeamRolePermissionsData = {
  roleId: number
  permissions: number[]
}

export interface RolePermissionsRepository {
  createRole: (teamId: number, formData: CreateTeamRoleData) => Promise<Role>
  getRoles: (teamId: number) => Promise<Role[]>
  showRole: (teamId: number, formData: GetTeamRolePermissionsData) => Promise<Role>
  updateRolePermissions: (teamId: number, formData: UpdateTeamRolePermissionsData) => Promise<Role>
}
