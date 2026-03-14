import type { Message } from "./Message"
import type { Participant } from "./Participant"

export interface Conversation {
  id: number
  name: string | null
  photo: string | null
  isPrivate: boolean
  isDirectMessage: boolean
  lastMessage?: Message
  participants?: Participant[]
  createdAt: string
  updatedAt: string
}
