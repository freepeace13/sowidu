import { PaginatedListResponse } from "@domain/shared/types"
import type { Conversation } from "./Conversation"

export interface ConversationRepository {
  getConversations(page: number, limit?: number): Promise<PaginatedListResponse<Conversation>>
  createConversation(recipients: string[], message?: string): Promise<Conversation>
  updateConversation(
    conversation: Pick<Conversation, "id"> & Partial<Pick<Conversation, "name" | "photo">>
  ): Promise<Conversation>
  getConversationById(conversationId: number): Promise<Conversation>
}
