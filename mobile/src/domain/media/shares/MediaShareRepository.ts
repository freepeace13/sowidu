import type { MediaUser } from "./MediaUser"
import type { MediaReadWritePermission } from "../types"

export type RevokeMediaShareData = {
  urn: string
}

export type AddMediaShareData = {
  urn: string
  scope: MediaReadWritePermission
}

export type SearchShareableUsersData = {
  keyword: string
  limit: number
}

export interface MediaShareRepository {
  search: (mediaId: string, formData: SearchShareableUsersData) => Promise<MediaUser[]>
  getSharedUsers: (mediaId: string) => Promise<MediaUser[]>
  shareTo: (mediaId: string, formData: AddMediaShareData) => Promise<MediaUser>
  unshareFrom: (mediaId: string, formData: RevokeMediaShareData) => Promise<void>
}
