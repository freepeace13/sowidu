import { Member } from "./members/Member"
import {
  MemberRepository,
  SearchNewTeamMembersData,
  SendTeamInvitationData,
  UpdateTeamMemberInfoData,
} from "./members/MemberRepository"

type BaseFormData<T = any> = T & {
  teamId: number
}

export interface ITeamMemberService {
  sendTeamInvitation: (formData: BaseFormData<SendTeamInvitationData>) => Promise<void>
  searchMembersForInvitation: (formData: BaseFormData<SearchNewTeamMembersData>) => Promise<any[]>
  getTeamMembers: (formData: BaseFormData) => Promise<Member[]>
  getTeamMemberInfo: (formData: BaseFormData<{ memberId: number }>) => Promise<Member>
  updateTeamMemberInfo: (formData: BaseFormData<UpdateTeamMemberInfoData>) => Promise<Member>
}

export const teamMemberService = (teamMemberRepository: MemberRepository): ITeamMemberService => ({
  async sendTeamInvitation({ teamId, ...formData }) {
    return await teamMemberRepository.sendInvitation(teamId, formData)
  },

  async searchMembersForInvitation({ teamId, ...formData }) {
    return await teamMemberRepository.getNewMembers(teamId, formData)
  },

  async getTeamMembers({ teamId }) {
    return await teamMemberRepository.getTeamMembers(teamId)
  },

  async getTeamMemberInfo({ teamId, ...formData }) {
    return await teamMemberRepository.getMembersInfo(teamId, formData)
  },

  async updateTeamMemberInfo({ teamId, ...formData }) {
    return await teamMemberRepository.updateMembersInfo(teamId, formData)
  },
})
