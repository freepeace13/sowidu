import { Permission } from "./Permission"

export interface Role {
  id: number
  teamId: number
  name: string
  permissions?: Permission[]
}
