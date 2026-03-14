import { Team } from "./team/Team"
import {
  CreateTeamData,
  GetTeamsData,
  TeamRepository,
  UpdateTeamAvatarData,
  UpdateTeamInfoData,
} from "./team/TeamRepository"

export interface ITeamService {
  getJoinedTeams: (formData?: GetTeamsData) => Promise<Team[]>
  getTeamInfo: (teamId: number) => Promise<Team>
  createTeam: (formData: CreateTeamData) => Promise<Team>
  changeTeamAvatar: (formData: UpdateTeamAvatarData & { teamId: number }) => Promise<string>
  updateTeamInfo: (formData: UpdateTeamInfoData & { teamId: number }) => Promise<Team>
}

export const teamService = (teamRepository: TeamRepository): ITeamService => ({
  async getJoinedTeams(formData) {
    return await teamRepository.getTeams({
      page: formData?.page,
      limit: formData?.limit,
    })
  },

  async getTeamInfo(teamId) {
    return await teamRepository.getTeamInfo(teamId)
  },

  async createTeam(formData) {
    return await teamRepository.createTeam(formData)
  },

  async changeTeamAvatar({ teamId, ...formData }) {
    return await teamRepository.updateAvatar(teamId, formData)
  },

  async updateTeamInfo({ teamId, ...formData }) {
    return await teamRepository.updateInfo(teamId, formData)
  },
})
