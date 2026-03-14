import { Role } from "./permissions/Role"
import {
  CreateTeamRoleData,
  GetTeamRolePermissionsData,
  RolePermissionsRepository,
  UpdateTeamRolePermissionsData,
} from "./permissions/RolePermissionsRepository"

type BaseFormData<T = any> = T & {
  teamId: number
}

export interface ITeamRolePermissionService {
  createTeamRole: (formData: BaseFormData<CreateTeamRoleData>) => Promise<Role>
  getTeamRoles: (formData: BaseFormData) => Promise<Role[]>
  getTeamRolePermissions: (formData: BaseFormData<GetTeamRolePermissionsData>) => Promise<Role>
  updateTeamRolePermissions: (
    formData: BaseFormData<UpdateTeamRolePermissionsData>
  ) => Promise<Role>
}

export const teamRolePermissionService = (
  rolePermissionRepository: RolePermissionsRepository
): ITeamRolePermissionService => ({
  async createTeamRole({ teamId, ...formData }) {
    return rolePermissionRepository.createRole(teamId, formData)
  },

  async getTeamRoles({ teamId }) {
    return await rolePermissionRepository.getRoles(teamId)
  },

  async getTeamRolePermissions({ teamId, ...formData }) {
    return await rolePermissionRepository.showRole(teamId, formData)
  },

  async updateTeamRolePermissions({ teamId, ...formData }) {
    return await rolePermissionRepository.updateRolePermissions(teamId, formData)
  },
})
