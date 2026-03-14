import { Nullable } from "@domain/shared/types"
import type { MediaReadWritePermission } from "../types"

export interface MediaUser {
  id: number
  urn: string
  name: string
  email: string
  avatar: string
  scopes: Nullable<MediaReadWritePermission>
  isOwner?: boolean
  canRead?: boolean
  canWrite?: boolean
}
