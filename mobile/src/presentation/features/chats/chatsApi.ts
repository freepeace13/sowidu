import { chatService } from "@application/main"
import type { Conversation } from "@domain/chats/Conversation"
import { Message } from "@domain/chats/Message"
import { sharedApi } from "@presentation/features/shared/api"

interface ListResult<T> {
  list: T[]
  lastPage: number
}

export const chatsApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    getConversations: build.query<ListResult<Conversation>, { page: number; limit?: number }>({
      queryFn: ({ page, limit = 10 }) => {
        return chatService
          .getConversations(page, limit)
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

    getConversationInfo: build.query<Conversation, { conversationId: number }>({
      queryFn: ({ conversationId }) => {
        return chatService
          .getConversationById(conversationId)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    createConversation: build.mutation<Conversation, { recipients: string[]; message?: string }>({
      queryFn: ({ recipients, message }) => {
        return chatService
          .createConversation(recipients, message)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    updateConversation: build.mutation<
      Conversation,
      Pick<Conversation, "id"> & Partial<Pick<Conversation, "name" | "photo">>
    >({
      queryFn: ({ id, name, photo }) => {
        return chatService
          .updateConversation({ id, name, photo })
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    getMessages: build.query<
      ListResult<Message>,
      { conversationId: number; page: number; limit?: number }
    >({
      queryFn: ({ conversationId, page, limit = 10 }) => {
        return chatService
          .getMessages(conversationId, page, limit)
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
  }),
})

export const {
  useGetConversationsQuery,
  useGetConversationInfoQuery,
  useGetMessagesQuery,
  useCreateConversationMutation,
  useUpdateConversationMutation,
} = chatsApi
