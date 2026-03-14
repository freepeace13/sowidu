import { ProfileRepository } from "@domain/user/ProfileRepository"
import { User } from "@domain/user/User"
import { User as UserSchema } from "@infrastructure/schema/User"
import { request } from "@infrastructure/api/request"
import { userTransformer } from "@infrastructure/transformers/UserTransformer"
import { UpdateUserAvatar, UpdateUserInfo } from "@infrastructure/api/urls"

export const userProfileRepository: ProfileRepository = {
  async updateAvatar({ avatar }) {
    const formData = new FormData()
    formData.append("_method", "PATCH")
    formData.append("avatar", {
      uri: avatar.uri,
      name: avatar.name,
      type: avatar.type,
    } as any)
    return await request<{ data: UserSchema }, string>({
      url: UpdateUserAvatar.replace({}),
      method: "POST",
      body: formData,
      transformResponse: ({ data }) => data.data?.photo,
    })
  },

  async updateInfo(params) {
    return await request<{ data: UserSchema }, User>({
      url: UpdateUserInfo.replace({}),
      method: "POST",
      body: {
        _method: "PATCH",
        firstName: params.firstName,
        lastName: params.lastName,
        birthdate: params.birthdate,
        gender: params.gender,
      },
      transformResponse: ({ data }) => userTransformer.transform(data.data),
    })
  },
}
