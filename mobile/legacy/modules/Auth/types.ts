import { TeamInfo } from "teams-module/types"

export type AccessToken = string

export interface UserInfo {
  id: number
  name: string | null
  email: string | null
  photo: string | null
  currentTeam?: undefined | TeamInfo
}
