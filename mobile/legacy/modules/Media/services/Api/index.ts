import { Api as CoreApi } from "core-module"

import * as MediaConstants from "../../constants"

type MediaType = (typeof MediaConstants.MediaTypes)[keyof typeof MediaConstants.MediaTypes]

const urlParams = (params: { [key: string]: any }) => {
  return new URLSearchParams(JSON.parse(JSON.stringify(params))).toString()
}

const transformToMediaFiles = (items) =>
  items.map((media) => ({
    id: media.id,
    title: media.title,
    file: {
      name: media.file.name,
      type: media.file.type,
      size: media.file.size,
      thumbnail: media.file.thumbnail,
    },
    shared: media.shared,
    uploadDate: media.uploadDate,
    policy: media.policy,
  }))

const mediaApi = CoreApi.injectEndpoints({
  endpoints: (builder) => ({
    getMediaFiles: builder.query({
      query: (params: { type?: MediaType }) => ({
        url: `media/?${urlParams(params)}`,
        method: "GET",
      }),
      transformResponse: (response) => transformToMediaFiles(response),
    }),

    getMediaDetails: builder.query({
      query: ({ id }: { id: string }) => ({
        url: `media/${id}`,
        method: "GET",
      }),
    }),

    updateMediaDetails: builder.mutation({
      query: ({ id, details }: { id: string; details: any }) => ({
        url: `media/${id}`,
        method: "PATCH",
        data: details,
      }),
    }),
  }),
})

export const { useGetMediaFilesQuery, useGetMediaDetailsQuery, useUpdateMediaDetailsMutation } =
  mediaApi

export default {
  useGetMediaFilesQuery,
  useGetMediaDetailsQuery,
  useUpdateMediaDetailsMutation,
}
