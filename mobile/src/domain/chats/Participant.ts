export interface Participant {
  id: number
  conversationId: number
  teamId?: number
  teamMembershipId?: number
  user: {
    id: number
    urn: string
    name: string
    email: string
    photo: string
  }
  createdAt: string
  updatedAt: string
}
