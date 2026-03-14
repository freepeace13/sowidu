import { Message } from "@domain/chats/Message"
import { MessageRepository } from "@domain/chats/MessageRepository"
import { PaginatedListResponse } from "@domain/shared/types"
import { request } from "@infrastructure/api/request"
import {
  DeleteChatConversationMessageUri,
  GetChatConversationMessagesUri,
  SendChatConversationMessageUri,
  UpdateChatConversationMessageUri,
} from "@infrastructure/api/urls"
import { ChatsMessageSchema } from "@infrastructure/schema/ChatsMessageSchema"
import { chatsMessageTransformer } from "@infrastructure/transformers/ChatsMessageTransformer"

export const chatsMessageRepository: MessageRepository = {
  async sendMessage(message: Omit<Message, "id" | "createdAt" | "updatedAt">) {
    return await request<ChatsMessageSchema, Message>({
      url: SendChatConversationMessageUri.replace({ conversationId: message.conversationId }),
      method: "POST",
      body: {
        type: message.type,
        message: message.body,
      },
      transformResponse: ({ data }) => chatsMessageTransformer.transform(data),
    })
  },

  async deleteMessage(message: Message) {
    return await request<boolean, boolean>({
      url: DeleteChatConversationMessageUri.replace({
        conversationId: message.conversationId,
        messageId: message.id,
      }),
      method: "DELETE",
      transformResponse: ({ data }) => data,
    })
  },

  async updateMessage(message: Omit<Message, "updatedAt" | "createdAt">) {
    return await request<ChatsMessageSchema, Message>({
      url: UpdateChatConversationMessageUri.replace({
        conversationId: message.conversationId,
        messageId: message.id,
      }),
      body: { message },
      method: "PATCH",
      transformResponse: ({ data }) => chatsMessageTransformer.transform(data),
    })
  },

  async getMessages(conversationId: number, page: number, limit?: number) {
    return await request<PaginatedListResponse<ChatsMessageSchema>, PaginatedListResponse<Message>>(
      {
        url: GetChatConversationMessagesUri.replace({ conversationId }),
        method: "GET",
        params: { page, limit },
        headers: {
          "X-Api-Features": "PaginatedResourceCollection",
        },
        transformResponse: ({ data }) => ({
          ...data,
          data: chatsMessageTransformer.collection(data.data),
        }),
      }
    )
  },
}
