import { PermissionGroup } from "./PermissionGroup"

export interface PermissionGroupRepository {
  getPermissionsByGroup: () => Promise<PermissionGroup[]>
}
