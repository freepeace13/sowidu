import { PermissionGroupRepository } from "./PermissionGroupRepository"
import { PermissionGroup } from "./PermissionGroup"

interface IPermissionGroupService {
  getGroupedPermissions: () => Promise<PermissionGroup[]>
}

export const permissionGroupService = (
  permissionGroupRepository: PermissionGroupRepository
): IPermissionGroupService => ({
  getGroupedPermissions: async () => {
    return await permissionGroupRepository
      .getPermissionsByGroup()
      .then((groups) => groups.map(({ label, permissions }) => ({ label, permissions })))
  },
})
