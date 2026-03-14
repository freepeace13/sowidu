import type { ChatsParticipantSchema } from "./ChatsParticipantSchema"
import type { ChatsMessageSchema } from "./ChatsMessageSchema"

export interface ChatsConversationSchema {
  id: number
  name: string
  photo: string
  lastMessage?: ChatsMessageSchema
  participants?: ChatsParticipantSchema[]
  private: boolean
  directMessage: boolean
  createdAt: string
  updatedAt: string
}
