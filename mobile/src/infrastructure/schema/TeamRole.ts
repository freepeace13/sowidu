export interface RolePermission {
  id: number
  name: string
  hasDirectPermission: boolean
}

export interface TeamRole {
  id: number
  teamId: number
  name: string
  permissions: RolePermission[]
}
