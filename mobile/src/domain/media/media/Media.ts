import type { MediaPolicy } from "../types"
import type { MediaFile } from "./MediaFile"

export interface Media {
  id: string
  title: string
  file: MediaFile
  shared: boolean
  uploadDate: string
  permission?: MediaPolicy
}
