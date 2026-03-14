import { Team } from "./Team"
import { Gender } from "@domain/shared/types"

export interface User {
  id: number
  urn: string
  fullName: string
  firstName: string
  lastName: string
  birthdate: string
  gender: Gender
  email: string
  photo: string
  currentTeam?: Team & { membershipId: number }
  permissions: string[]
}
