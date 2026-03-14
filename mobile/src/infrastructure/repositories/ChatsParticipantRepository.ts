import { Participant } from "@domain/chats/Participant"
import { ParticipantRepository } from "@domain/chats/ParticipantRepository"
import { request } from "@infrastructure/api/request"
import {
  AddChatConversationParticipantUri,
  GetChatConversatioInfoUri,
  RemoveChatConversationParticipantUri,
} from "@infrastructure/api/urls"
import { ChatsParticipantSchema } from "@infrastructure/schema/ChatsParticipantSchema"
import { chatsParticipantTransformer } from "@infrastructure/transformers/ChatsParticipantTransformer"

export const chatsParticipantRepository: ParticipantRepository = {
  async getParticipants(conversationId) {
    return await request<ChatsParticipantSchema[], Participant[]>({
      url: GetChatConversatioInfoUri.replace({ conversationId }),
      method: "GET",
      transformResponse: ({ data }) => chatsParticipantTransformer.collection(data),
    })
  },

  async addParticipant(participant: Omit<Participant, "id" | "createdAt" | "updatedAt">) {
    return await request<ChatsParticipantSchema, Participant>({
      url: AddChatConversationParticipantUri.replace({
        conversationId: participant.conversationId,
      }),
      method: "POST",
      body: {
        urn: participant.user.urn,
      },
      transformResponse: ({ data }) => chatsParticipantTransformer.transform(data),
    })
  },

  async removeParticipant(participant: Participant) {
    return await request<boolean, boolean>({
      url: RemoveChatConversationParticipantUri.replace({
        conversationId: participant.conversationId,
        participationId: participant.id,
      }),
      method: "DELETE",
      transformResponse: ({ data }) => data,
    })
  },
}
