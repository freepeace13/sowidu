import { Nullable } from "@domain/shared/types"

export interface InstitutionType {
  id: number
  title: string
  abbrev: string
}

export interface LegalForm {
  id: number
  title: string
  abbrev: string
}

export interface Team {
  id: number
  urn: string
  name: string
  photoURL: string
  legalForm: Nullable<{
    id: number
    name: string
  }>
  institutionType: Nullable<{
    id: number
    name: string
  }>
}

export interface CurrentTeam extends Team {
  membershipId: number
}
