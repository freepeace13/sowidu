export interface ChatsParticipantSchema {
  id: number
  conversationId: number
  urn: string
  userId: number
  name: string
  firstName: string
  lastName: string
  email: string
  teamId?: number
  teamMembershipId?: number
  photo: string
  createdAt: string
  updatedAt: string
}
