import { Nullable } from "@domain/shared/types"
import type {
  MediaPolicy,
  MediaReadWritePermission,
  ImageResponsive,
  MediaType,
} from "@domain/media/types"

export interface MediaUser {
  id: number
  urn: string
  name: string
  email: string
  photo: string
  isOwner?: boolean
  scopes?: Nullable<MediaReadWritePermission>
  canRead?: boolean
  canWrite?: boolean
}

export interface MediaFile<T extends MediaType> {
  name: string
  type: T
  size: number
  uri: string
  thumbnail: string
}

export interface Media {
  id: string
  title: string
  file: MediaPdf | MediaVideo | MediaImage
  shared: boolean
  uploadDate: string
  policy: MediaPolicy
}

export type MediaPdf = MediaFile<MediaType.Pdf>
export type MediaVideo = MediaFile<MediaType.Video>
export type MediaImage = MediaFile<MediaType.Image> & {
  responsive: ImageResponsive[]
}
