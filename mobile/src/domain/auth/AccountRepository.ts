import { User } from "@domain/user/User"

export interface AccountRepository {
  switchAccount: (urn: string) => Promise<User["currentTeam"]>
}
