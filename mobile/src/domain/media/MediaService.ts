import { PaginatedListResponse } from "@domain/shared/types"
import { Media } from "./media/Media"
import { MediaRepository, GetMediaFilesData, UpdateMediaInfoData } from "./media/MediaRepository"
import { PendingUpload } from "./upload/PendingUpload"

type BaseFormData<T = void> = T & {
  mediaId: string
}

interface IMediaService {
  upload: (formData: PendingUpload) => Promise<Media>
  getFiles: (formData: GetMediaFilesData) => Promise<PaginatedListResponse<Media>>
  getInfo: (mediaId: string) => Promise<Media>
  updateInfo: (formData: BaseFormData<UpdateMediaInfoData>) => Promise<Media>
}

export const mediaService = (mediaRepository: MediaRepository): IMediaService => ({
  async upload(formData) {
    return await mediaRepository.upload(formData)
  },

  async getFiles(formData) {
    return await mediaRepository.getFiles(formData)
  },

  async getInfo(mediaId) {
    return mediaRepository.getInfo(mediaId)
  },

  async updateInfo({ mediaId, ...formData }) {
    return await mediaRepository.updateInfo(mediaId, formData)
  },
})
