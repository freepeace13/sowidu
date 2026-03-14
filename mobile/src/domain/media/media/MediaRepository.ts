import {
  FileSystemNetworkTaskProgressCallback,
  UploadProgressData,
  FileSystemUploadOptions,
} from "expo-file-system"

import { Media } from "./Media"
import { PendingUpload } from "../upload/PendingUpload"
import { PaginatedListResponse } from "@domain/shared/types"

export type GetMediaFilesData = {
  page: number
  limit?: number
}

export interface UploadArgs {
  fileUri: string
  options?: FileSystemUploadOptions
  callback?: FileSystemNetworkTaskProgressCallback<UploadProgressData>
}

export interface UpdateMediaInfoData {
  name: string
}

export interface MediaRepository {
  getFiles(params: GetMediaFilesData): Promise<PaginatedListResponse<Media>>
  getInfo(mediaId: string): Promise<Media>
  updateInfo(mediaId: string, data: UpdateMediaInfoData): Promise<Media>
  upload(params: PendingUpload): Promise<Media>
}
