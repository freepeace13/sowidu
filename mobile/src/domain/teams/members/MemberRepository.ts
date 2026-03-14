import { CurrencyCode } from "@domain/shared/types"
import { Member } from "./Member"

export type GetTeamMemberInfoData = {
  memberId: number
}

export type SearchNewTeamMembersData = {
  keyword: string
  limit: number
}

export type UpdateTeamMemberInfoData = {
  memberId: number
  roles?: string[]
  contactNumber?: any
  rates?: {
    currency: CurrencyCode
    rate: string
  }
}

export type SendTeamInvitationData = {
  email: string
  role: string
  message?: string
}

export interface MemberRepository {
  getTeamMembers: (teamId: number) => Promise<Member[]>
  getMembersInfo: (teamId: number, formData: GetTeamMemberInfoData) => Promise<Member>
  getNewMembers: (teamId: number, formData: SearchNewTeamMembersData) => Promise<any[]>
  updateMembersInfo: (teamId: number, formData: UpdateTeamMemberInfoData) => Promise<Member>
  sendInvitation: (teamId: number, formData: SendTeamInvitationData) => Promise<void>
}
