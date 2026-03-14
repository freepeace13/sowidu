import { createTransformer } from "@infrastructure/utils/transformer"
import type { ChatsConversationSchema } from "@infrastructure/schema/ChatsConversationSchema"
import type { Conversation } from "@domain/chats/Conversation"
import { chatsParticipantTransformer } from "./ChatsParticipantTransformer"
import { chatsMessageTransformer } from "./ChatsMessageTransformer"

export const chatsConversationTransformer = createTransformer<
  ChatsConversationSchema,
  Conversation
>((schema) => ({
  id: schema.id,
  name: schema.name,
  photo: schema.photo,
  isPrivate: schema.private,
  isDirectMessage: schema.directMessage,
  lastMessage: schema.lastMessage
    ? chatsMessageTransformer.transform(schema.lastMessage)
    : undefined,
  participants: schema.participants
    ? chatsParticipantTransformer.collection(schema.participants)
    : undefined,
  createdAt: schema.createdAt,
  updatedAt: schema.updatedAt,
}))
