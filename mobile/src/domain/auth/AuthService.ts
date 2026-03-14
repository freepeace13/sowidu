import { TokenRepository } from "@domain/user/TokenRepository"
import { User } from "@domain/user/User"
import { UserRepository, UserLoginData } from "@domain/user/UserRepository"
import { AccountRepository } from "./AccountRepository"

interface IAuthService {
  tokenUser: () => Promise<User>
  retrieveToken: () => Promise<string | null>
  revokeToken: () => Promise<void>
  switchAccount: (urn: string) => Promise<User["currentTeam"]>
  requestToken: (credentials: UserLoginData) => Promise<string>
}

export const authService = (
  tokenRepository: TokenRepository,
  userRepository: UserRepository,
  accountRepository: AccountRepository
): IAuthService => ({
  async tokenUser() {
    return await userRepository.user()
  },

  async retrieveToken() {
    return await tokenRepository.current()
  },

  async switchAccount(urn) {
    return await accountRepository.switchAccount(urn)
  },

  async revokeToken() {
    await tokenRepository.delete()
  },

  async requestToken(credentials) {
    let token = await userRepository.login(credentials)
    await tokenRepository.store(token)
    return token
  },
})
