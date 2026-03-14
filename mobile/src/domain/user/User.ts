import type { CurrentTeam } from "@domain/teams/team/Team"

export interface User {
  id: number
  urn: string
  fullName: string
  firstName: string
  lastName: string
  email: string
  photoURL: string
  birthdate?: string
  gender?: string
  currentTeam?: CurrentTeam | null
  permissions: string[]
}
