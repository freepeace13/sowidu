import { Nullable } from "@domain/shared/types"
import { User } from "./User"

export type UpdateUserAvatarData = {
  avatar: {
    uri: string
    name: string
    type: "image/jpeg" | "image/png"
  }
}

export type UpdateUserInfoData = {
  firstName: string
  lastName: string
  birthdate: Nullable<Date>
  gender: Nullable<string>
}

export interface ProfileRepository {
  updateInfo(params: UpdateUserInfoData): Promise<User>
  updateAvatar(params: UpdateUserAvatarData): Promise<string>
}
