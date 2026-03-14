import type { MessageType } from "@domain/chats/Message"
import type { ChatsParticipantSchema } from "./ChatsParticipantSchema"

export interface ChatsMessageSchema {
  id: number
  type: MessageType
  body: string
  data: any[]
  participationId: number
  conversationId: number
  sender: ChatsParticipantSchema
  createdAt: string
  updatedAt: string
}
