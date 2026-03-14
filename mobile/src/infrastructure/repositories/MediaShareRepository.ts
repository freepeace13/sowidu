import { MediaShareRepository } from "@domain/media/shares/MediaShareRepository"
import { MediaUser } from "@domain/media/shares/MediaUser"

import {
  GetMediaInfoUri,
  GetMediaSharedUsersUri,
  AddMediaShareUri,
  RevokeMediaShareUri,
} from "@infrastructure/api/urls"

import { MediaUser as MediaUserSchema } from "@infrastructure/schema/Media"
import { mediaUserTransformer } from "@infrastructure/transformers/MediaUserTransformer"
import { request } from "@infrastructure/api/request"

export const mediaShareRepository: MediaShareRepository = {
  async search(mediaId, formData) {
    return await request<{ data: MediaUserSchema[] }, MediaUser[]>({
      url: GetMediaInfoUri.replace({ mediaId }),
      method: "GET",
      params: {
        q: formData.keyword,
        limit: formData.limit,
      },
      transformResponse: ({ data }) => mediaUserTransformer.collection(data.data),
    })
  },

  async getSharedUsers(mediaId) {
    return await request<{ data: MediaUserSchema[] }, MediaUser[]>({
      url: GetMediaSharedUsersUri.replace({ mediaId }),
      method: "GET",
      transformResponse: ({ data }) => mediaUserTransformer.collection(data.data),
    })
  },

  async shareTo(mediaId, params) {
    return await request<{ data: MediaUserSchema }, MediaUser>({
      url: AddMediaShareUri.replace({ mediaId }),
      method: "PUT",
      body: {
        user: params.urn,
        scope: params.scope,
      },
      transformResponse: ({ data }) => mediaUserTransformer.transform(data.data),
    })
  },

  async unshareFrom(mediaId, params) {
    return await request<void, void>({
      url: RevokeMediaShareUri.replace({ mediaId }),
      method: "DELETE",
      body: {
        user: params.urn,
      },
      transformResponse: ({ data }) => data,
    })
  },
}
