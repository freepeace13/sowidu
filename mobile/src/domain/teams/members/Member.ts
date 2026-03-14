import { CurrencyCode } from "@domain/shared/types"

export interface MemberUserInfo {
  id: number
  name: string
  email: string
  photoURL: string
}

export interface Member {
  teamId: number
  memberId: number
  teamRoles: string[]
  role: string
  ownsTeam: boolean
  userInfo: MemberUserInfo
  rates?: {
    currency: CurrencyCode
    rate: number
  }
}
