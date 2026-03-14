import {
  AddMediaShareData,
  MediaShareRepository,
  RevokeMediaShareData,
  SearchShareableUsersData,
} from "./shares/MediaShareRepository"
import { MediaUser } from "./shares/MediaUser"

type BaseFormData<T = void> = T & {
  mediaId: string
}

interface IMediaShareService {
  search: (formData: BaseFormData<SearchShareableUsersData>) => Promise<MediaUser[]>
  getSharedUsers: (mediaId: string) => Promise<MediaUser[]>
  shareTo: (formData: BaseFormData<AddMediaShareData>) => Promise<MediaUser>
  unshareFrom: (params: BaseFormData<RevokeMediaShareData>) => Promise<void>
}

export const mediaShareService = (
  mediaShareRepository: MediaShareRepository
): IMediaShareService => ({
  async search({ mediaId, ...formData }) {
    return await mediaShareRepository.search(mediaId, formData)
  },

  async getSharedUsers(mediaId) {
    return await mediaShareRepository.getSharedUsers(mediaId)
  },

  async shareTo({ mediaId, ...formData }) {
    return await mediaShareRepository.shareTo(mediaId, formData)
  },

  async unshareFrom({ mediaId, ...formData }) {
    return await mediaShareRepository.unshareFrom(mediaId, formData)
  },
})
