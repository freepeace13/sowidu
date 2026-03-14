import type { Team } from "./Team"

export type CreateTeamData = {
  name: string
  institutionTypeId: number
  legalFormId: number
}

export type UpdateTeamInfoData = {
  name: string
  institutionType?: number
  legalForm?: number
}

export type UpdateTeamAvatarData = {
  image: {
    uri: string
    name: string
    type: "image/jpeg" | "image/png"
  }
}

export type GetTeamsData = {
  page?: number
  limit?: number
}

export interface TeamRepository {
  getTeams(formData?: GetTeamsData): Promise<Team[]>
  getTeamInfo(teamId: number): Promise<Team>
  createTeam(formData: CreateTeamData): Promise<Team>
  updateAvatar(teamId: number, formData: UpdateTeamAvatarData): Promise<string>
  updateInfo(teamId: number, formData: UpdateTeamInfoData): Promise<Team>
}
