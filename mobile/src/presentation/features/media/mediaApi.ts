import { mediaService, mediaShareService } from "@application/main"
import { Media } from "@domain/media/media/Media"
import { GetMediaFilesData, UpdateMediaInfoData } from "@domain/media/media/MediaRepository"
import {
  AddMediaShareData,
  RevokeMediaShareData,
  SearchShareableUsersData,
} from "@domain/media/shares/MediaShareRepository"
import { MediaUser } from "@domain/media/shares/MediaUser"
import { sharedApi } from "@presentation/features/shared/api"

interface ListResult<T> {
  list: T[]
  lastPage: number
}

const mediaApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    getFiles: build.query<ListResult<Media>, GetMediaFilesData>({
      queryFn: ({ page, limit = 10 }) => {
        return mediaService
          .getFiles({ page, limit })
          .then(({ data, meta }) => ({ data: { list: data, lastPage: meta.lastPage } }))
          .catch((error) => ({ error }))
      },
      serializeQueryArgs: ({ endpointName }) => {
        return endpointName
      },
      merge: (currentCache, newItems) => {
        currentCache.list.push(...newItems.list)
      },
      forceRefetch({ currentArg, previousArg }) {
        return currentArg !== previousArg
      },
    }),

    getFileInfo: build.query<Media, { mediaId: string }>({
      queryFn: ({ mediaId }) => {
        return mediaService
          .getInfo(mediaId)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    updateFileInfo: build.mutation<Media, UpdateMediaInfoData & { mediaId: string }>({
      queryFn: ({ mediaId, name }) => {
        return mediaService
          .updateInfo({ mediaId, name })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted({ mediaId }, { dispatch, queryFulfilled }) {
        try {
          const { data } = await queryFulfilled
          dispatch(
            mediaApi.util.updateQueryData("getFiles" as const, { page: 1, limit: 10 }, (draft) => {
              const index = draft.list.findIndex((item) => item.id === mediaId)
              if (index !== -1) {
                draft.list.splice(index, 1, {
                  ...draft.list[index],
                  ...data,
                })
              }
            })
          )
        } catch {}
      },
    }),

    searchShareableUsers: build.query<MediaUser[], SearchShareableUsersData & { mediaId: string }>({
      queryFn: ({ mediaId, keyword, limit }) =>
        mediaShareService
          .search({ mediaId, keyword, limit })
          .then((data) => ({ data }))
          .catch((error) => ({ error })),
    }),

    getSharedUsers: build.query<MediaUser[], { mediaId: string }>({
      queryFn: ({ mediaId }) =>
        mediaShareService
          .getSharedUsers(mediaId)
          .then((data) => ({ data }))
          .catch((error) => ({ error })),
    }),

    shareTo: build.mutation<MediaUser, AddMediaShareData & { mediaId: string }>({
      queryFn: ({ mediaId, urn, scope }) => {
        return mediaShareService
          .shareTo({ mediaId, urn, scope })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    unshareFrom: build.mutation<void, RevokeMediaShareData & { mediaId: string }>({
      queryFn: ({ mediaId, urn }) => {
        return mediaShareService
          .unshareFrom({ mediaId, urn })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),
  }),
})

export const resetMediaApiState = mediaApi.util.resetApiState
export const updateMediaApiQueryData = mediaApi.util.updateQueryData

export const {
  useGetFilesQuery,
  useGetFileInfoQuery,
  useUpdateFileInfoMutation,
  useSearchShareableUsersQuery,
  useGetSharedUsersQuery,
  useShareToMutation,
  useUnshareFromMutation,
} = mediaApi
