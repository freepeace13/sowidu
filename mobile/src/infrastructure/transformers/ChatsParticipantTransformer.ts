import { createTransformer } from "@infrastructure/utils/transformer"
import type { ChatsParticipantSchema } from "@infrastructure/schema/ChatsParticipantSchema"
import type { Participant } from "@domain/chats/Participant"

export const chatsParticipantTransformer = createTransformer<ChatsParticipantSchema, Participant>(
  (schema) => ({
    id: schema.id,
    conversationId: schema.conversationId,
    teamId: schema.teamId,
    teamMembershipId: schema.teamMembershipId,
    user: {
      id: schema.userId,
      name: schema.name,
      urn: schema.urn,
      email: schema.email,
      photo: schema.photo,
    },
    createdAt: schema.createdAt,
    updatedAt: schema.updatedAt,
  })
)
