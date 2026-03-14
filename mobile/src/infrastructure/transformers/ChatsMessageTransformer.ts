import { createTransformer } from "@infrastructure/utils/transformer"
import type { ChatsMessageSchema } from "@infrastructure/schema/ChatsMessageSchema"
import type { Message } from "@domain/chats/Message"
import { chatsParticipantTransformer } from "./ChatsParticipantTransformer"

export const chatsMessageTransformer = createTransformer<ChatsMessageSchema, Message>((schema) => ({
  id: schema.id,
  type: schema.type,
  body: schema.body,
  data: schema.data,
  participationId: schema.participationId,
  conversationId: schema.conversationId,
  sender: chatsParticipantTransformer.transform(schema.sender),
  updatedAt: schema.updatedAt,
  createdAt: schema.createdAt,
}))
