import { User } from "@domain/user/User"
import * as Permissions from "@application/constants/permissions"

type PermissionValue = (typeof Permissions)[keyof typeof Permissions]

export const useAuthorization = (user?: User) => {
  const userPermissions = (user?.permissions || []) as PermissionValue[]
  const isTeamSession = user?.currentTeam?.id !== undefined

  const hasPermissionTo = (name: PermissionValue) => {
    return !isTeamSession || userPermissions.includes(name)
  }

  const hasAnyPermission = (names: PermissionValue[]) => {
    return !isTeamSession || userPermissions.some((i) => names.includes(i))
  }

  const hasAllPermission = (names: PermissionValue[]) => {
    return !isTeamSession || userPermissions.every((i) => names.includes(i))
  }

  const doesntHavePermissionTo = (names: PermissionValue) => {
    return !hasPermissionTo(names)
  }

  const doesntHaveAnyPermission = (permissions: PermissionValue[]) => {
    return !hasAnyPermission(permissions)
  }

  const doesntHaveAllPermission = (permissions: PermissionValue[]) => {
    return !hasAllPermission(permissions)
  }

  return {
    hasPermissionTo,
    hasAnyPermission,
    hasAllPermission,
    doesntHavePermissionTo,
    doesntHaveAnyPermission,
    doesntHaveAllPermission,
  }
}
