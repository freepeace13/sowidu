import type { Participant } from "./Participant"

export enum MessageType {
  Text = "text",
  Attachment = "attachment",
}

export interface Message {
  id: number
  type: MessageType
  body: string
  data: any[]
  participationId: number
  conversationId: number
  sender: Participant
  updatedAt: string
  createdAt: string
}
