import { TokenRepository } from "@domain/user/TokenRepository"
import { asyncPersistedStorage } from "@infrastructure/utils/asyncPersistedStorage"

const STORAGE_KEY = "auth_token"

export const tokenRepository: TokenRepository = {
  async current() {
    return await asyncPersistedStorage.get(STORAGE_KEY)
  },

  async store(token) {
    await asyncPersistedStorage.store(STORAGE_KEY, token)
  },

  async delete() {
    await asyncPersistedStorage.clear(STORAGE_KEY)
  },
}
