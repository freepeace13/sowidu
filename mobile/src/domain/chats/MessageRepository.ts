import { PaginatedListResponse } from "@domain/shared/types"
import { Message } from "./Message"

export interface MessageRepository {
  sendMessage(message: Omit<Message, "id" | "createdAt" | "updatedAt">): Promise<Message>
  deleteMessage(message: Message): Promise<boolean>
  updateMessage(message: Partial<Omit<Message, "updatedAt" | "createdAt">>): Promise<Message>
  getMessages(
    conversationId: number,
    page: number,
    limit?: number
  ): Promise<PaginatedListResponse<Message>>
}
