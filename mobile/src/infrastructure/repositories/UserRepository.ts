import { UserRepository } from "@domain/user/UserRepository"
import {
  GetCurrentUserUri,
  LoginUserUri,
  RegisterUserUri,
  LogoutUserUri,
} from "@infrastructure/api/urls"
import { request } from "@infrastructure/api/request"
import { User as UserSchema } from "@infrastructure/schema/User"
import { User } from "@domain/user/User"
import { userTransformer } from "@infrastructure/transformers/UserTransformer"

export const userRepository: UserRepository = {
  async user() {
    return await request<{ data: UserSchema }, User>({
      url: GetCurrentUserUri.replace({}),
      method: "GET",
      transformResponse: ({ data }) => userTransformer.transform(data.data),
    })
  },

  async login(credentials) {
    return await request<string, string>({
      url: LoginUserUri.replace({}),
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        email: credentials.email,
        password: credentials.password,
        device: credentials.device,
      }),
      transformResponse: ({ data }) => data,
    })
  },

  async register(account) {
    return await request<string, string>({
      url: RegisterUserUri.replace({}),
      method: "POST",
      body: {
        email: account.email,
        password: account.password,
        name: account.name,
      },
      transformResponse: ({ data }) => data,
    })
  },

  async logout() {
    return await request<void, void>({
      url: LogoutUserUri.replace({}),
      method: "POST",
      transformResponse: ({ data }) => data,
    })
  },
}
