import { CurrencyCode } from "@domain/shared/types"
import { User } from "./User"

export interface TeamMember extends Omit<User, "currentTeam" | "permissions"> {
  teamId: number
  membershipId: number
  isOwner: boolean
  teamRole: string
  roles: any[]
  rates?: {
    rate: number
    currency: CurrencyCode
  }
}
