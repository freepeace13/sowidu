import { Media } from "@domain/media/media/Media"
import { MediaRepository } from "@domain/media/media/MediaRepository"

import { PaginatedListResponse } from "@domain/shared/types"
import { Media as MediaSchema } from "@infrastructure/schema/Media"
import { GetMediaFilesUri, GetMediaInfoUri, UpdateMediaInfoUri } from "@infrastructure/api/urls"
import { mediaTransformer } from "@infrastructure/transformers/MediaTransformer"
import { request } from "@infrastructure/api/request"

export const mediaRepository: MediaRepository = {
  getInfo: async (mediaId) => {
    return await request<{ data: MediaSchema }, Media>({
      url: GetMediaInfoUri.replace({ mediaId }),
      method: "GET",
      transformResponse: ({ data }) => mediaTransformer.transform(data.data),
    })
  },

  getFiles: async ({ page, limit = 10 }) => {
    return await request<PaginatedListResponse<MediaSchema>, PaginatedListResponse<Media>>({
      url: GetMediaFilesUri.replace({}),
      method: "GET",
      params: { page, limit },
      headers: {
        "X-Api-Features": "PaginatedResourceCollection",
      },
      transformResponse: ({ data }) => ({
        ...data,
        data: mediaTransformer.collection(data.data),
      }),
    })
  },

  updateInfo: async (mediaId, params) => {
    return await request<{ data: MediaSchema }, Media>({
      url: UpdateMediaInfoUri.replace({ mediaId }),
      method: "PATCH",
      body: {
        name: params.name,
      },
      transformResponse: ({ data }) => mediaTransformer.transform(data.data),
    })
  },

  upload: async (params) => {
    return await params.promise.then((result) =>
      mediaTransformer.transform(JSON.parse(result.body).data)
    )
  },
}
