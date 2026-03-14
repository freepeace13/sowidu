import { chatService } from "@application/main"
import { ChatMessage } from "@domain/chats/Message"
import { Conversation } from "@domain/chats/Conversation"
import { sharedApi } from "@presentation/features/shared/api"

interface ListResult<T> {
  list: T[]
  lastPage: number
}

export const chatsApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    getConversations: build.query<ListResult<Conversation>, void>({
      queryFn: () => {
        return chatService
          .getConversations()
          .then(({ data, meta }) => ({
            data: {
              list: data,
              lastPage: meta.lastPage,
            },
          }))
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

    getConversationById: build.query<ListResult<ChatMessage>, { conversationId: number }>({
      queryFn: ({ conversationId }) => {
        return chatService
          .getConversationById(conversationId)
          .then(({ data, meta }) => ({
            data: {
              list: data,
              lastPage: meta.lastPage,
            },
          }))
          .catch((error) => ({ error }))
      },
    }),
  }),
})

export const { useGetConversationsQuery, useGetConversationByIdQuery } = chatsApi
