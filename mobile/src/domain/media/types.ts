export enum MediaType {
  Image = "image",
  Video = "video",
  Pdf = "pdf",
}

export enum MediaReadWritePermission {
  Read = "r",
  ReadWrite = "rw",
}

export interface ImageResponsive {
  uri: string
  width: number
  height: number
}

export interface MediaPolicy {
  canOpen: boolean
  canShare: boolean
  canDownload: boolean
  canRename: boolean
  canDelete: boolean
}
