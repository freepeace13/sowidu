import { Conversation } from "./Conversation"
import { ConversationRepository } from "./ConversationRepository"
import { MessageRepository } from "./MessageRepository"
import { ParticipantRepository } from "./ParticipantRepository"

export const chatService = (
  conversationRepository: ConversationRepository,
  messageRepository: MessageRepository,
  participantRepository: ParticipantRepository
) => ({
  async getConversations(page: number = 1, limit: number = 12) {
    return await conversationRepository.getConversations(page, limit)
  },

  async getConversationById(conversationId: number) {
    return await conversationRepository.getConversationById(conversationId)
  },

  async createConversation(recipients: string[], message?: string) {
    return await conversationRepository.createConversation(recipients, message)
  },

  async updateConversation(
    conversation: Pick<Conversation, "id"> & Partial<Pick<Conversation, "name" | "photo">>
  ) {
    return await conversationRepository.updateConversation(conversation)
  },

  async getMessages(conversationId: number, page: number = 1, limit: number = 12) {
    return await messageRepository.getMessages(conversationId, page, limit)
  },
})
