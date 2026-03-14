import { ProfileRepository, UpdateUserAvatarData, UpdateUserInfoData } from "./ProfileRepository"
import { TokenRepository } from "./TokenRepository"
import { User } from "./User"
import { UserRepository, UserRegisterData } from "./UserRepository"

interface IUserService {
  registerUser: (user: UserRegisterData) => Promise<string>
  changeUserAvatar: (formData: UpdateUserAvatarData) => Promise<string>
  updateUserInfo: (formData: UpdateUserInfoData) => Promise<User>
}

export const userService = (
  tokenRepository: TokenRepository,
  userRepository: UserRepository,
  profileRepository: ProfileRepository
): IUserService => ({
  registerUser: async (formData) => {
    const token = await userRepository.register(formData)
    await tokenRepository.store(token)
    return token
  },

  changeUserAvatar: async (formData) => {
    return await profileRepository.updateAvatar(formData)
  },

  updateUserInfo: async (formData) => {
    return await profileRepository.updateInfo(formData)
  },
})
