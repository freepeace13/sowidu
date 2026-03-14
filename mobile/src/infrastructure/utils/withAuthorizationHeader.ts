import { tokenRepository } from "@infrastructure/repositories/TokenRepository"

interface ObjectType {
  headers?: Record<string, string | number | boolean>
}

export const withAuthorizationHeader = async <T extends ObjectType>(obj: T): Promise<T> => {
  const token = await tokenRepository.current()
  if (token) {
    obj.headers = {
      ...obj.headers,
      Authorization: `Bearer ${token}`,
    }
  }
  return obj
}
