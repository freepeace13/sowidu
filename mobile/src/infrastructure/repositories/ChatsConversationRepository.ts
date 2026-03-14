import { Conversation } from "@domain/chats/Conversation"
import { ConversationRepository } from "@domain/chats/ConversationRepository"
import { PaginatedListResponse } from "@domain/shared/types"
import { request } from "@infrastructure/api/request"
import {
  CreateChatConversationUri,
  GetChatConversatioInfoUri,
  GetChatConversationsUri,
  UpdateChatConversationInfoUri,
} from "@infrastructure/api/urls"
import { ChatsConversationSchema } from "@infrastructure/schema/ChatsConversationSchema"
import { chatsConversationTransformer } from "@infrastructure/transformers/ChatsConversationTransformer"

export const chatsConversationRepository: ConversationRepository = {
  async createConversation(recipients: string[], message?: string) {
    return await request<ChatsConversationSchema, Conversation>({
      url: CreateChatConversationUri.replace({}),
      method: "POST",
      body: {
        recipients,
        message,
      },
      transformResponse: ({ data }) => chatsConversationTransformer.transform(data),
    })
  },

  async getConversations(page: number, limit?: number) {
    return await request<
      PaginatedListResponse<ChatsConversationSchema>,
      PaginatedListResponse<Conversation>
    >({
      url: GetChatConversationsUri.replace({}),
      method: "GET",
      params: { page, limit },
      headers: {
        "X-Api-Features": "PaginatedResourceCollection",
      },
      transformResponse: ({ data }) => ({
        ...data,
        data: chatsConversationTransformer.collection(data.data),
      }),
    })
  },

  async getConversationById(conversationId: number) {
    return await request<ChatsConversationSchema, Conversation>({
      url: GetChatConversatioInfoUri.replace({ conversationId }),
      method: "GET",
      transformResponse: ({ data }) => chatsConversationTransformer.transform(data),
    })
  },

  async updateConversation(
    conversation: Pick<Conversation, "id"> & Partial<Pick<Conversation, "name" | "photo">>
  ) {
    return await request<ChatsConversationSchema, Conversation>({
      url: UpdateChatConversationInfoUri.replace({ conversationId: conversation.id }),
      method: "POST",
      body: {
        _method: "PATCH",
        name: conversation.name,
        photo: conversation.photo,
      },
      transformResponse: ({ data }) => chatsConversationTransformer.transform(data),
    })
  },
}
