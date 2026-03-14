import { Nullable } from "@domain/shared/types"

export interface Team {
  id: number
  urn: string
  name: string
  photo: string
  legalForm: Nullable<{
    id: number
    name: string
  }>
  institutionType: Nullable<{
    id: number
    name: string
  }>
}
