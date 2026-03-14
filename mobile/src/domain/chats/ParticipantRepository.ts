import { Participant } from "./Participant"

export interface ParticipantRepository {
  getParticipants(conversationId: number): Promise<Participant[]>
  addParticipant(
    participant: Omit<Participant, "id" | "createdAt" | "updatedAt">
  ): Promise<Participant>
  removeParticipant(participant: Participant): Promise<boolean>
}
