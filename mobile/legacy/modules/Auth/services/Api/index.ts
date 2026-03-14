import { Api as CoreApi } from "core-module"
import * as Device from "expo-device"
import { TeamInfo } from "teams-module/types"

import { AccessToken, UserInfo } from "../../types"

type EmailLoginData = {
  email: string
  password: string
}

type EmailRegistrationData = {
  firstName: string
  lastName: string
  email: string
  password: string
  confirmPassword: string
}

export const createUser = (params): UserInfo => {
  return {
    id: params.id,
    name: params.name,
    email: params.email,
    photo: params.photo,
    currentTeam: params.currentTeam && {
      id: params.currentTeam.id,
      name: params.currentTeam.name,
      photo: params.currentTeam.photo,
    },
  } as UserInfo
}

export const authApi = CoreApi.injectEndpoints({
  endpoints: (builder) => ({
    getUserInfo: builder.query<UserInfo, void>({
      query: () => ({
        url: "user",
        method: "GET",
      }),
      keepUnusedDataFor: 3600 * 24, // 1 day
      providesTags: ["User"],
      transformResponse: (baseQueryReturnValue): UserInfo => {
        return createUser(baseQueryReturnValue)
      },
    }),

    loginWithEmail: builder.mutation<AccessToken, Partial<EmailLoginData>>({
      query: (data) => ({
        url: "auth/login",
        method: "POST",
        data: {
          email: data.email,
          password: data.password,
          device: Device.deviceName,
        },
      }),
    }),

    registerWithEmail: builder.mutation<undefined, Partial<EmailRegistrationData>>({
      query: (data) => ({
        url: "auth/register",
        method: "POST",
        body: {
          firstName: data.firstName,
          lastName: data.lastName,
          email: data.email,
          password: data.password,
          confirmPassword: data.confirmPassword,
        },
      }),
    }),

    logout: builder.mutation<null, void>({
      queryFn: () => ({ data: null }),
    }),
  }),
})

export const { loginWithEmail, registerWithEmail, getUserInfo, logout } = authApi.endpoints

export const {
  useLoginWithEmailMutation,
  useRegisterWithEmailMutation,
  useGetUserInfoQuery,
  useLogoutMutation,
} = authApi

export default {
  getUserInfo,
  loginWithEmail,
  registerWithEmail,
  logout,
  useLogoutMutation,
  useGetUserInfoQuery,
  useLoginWithEmailMutation,
  useRegisterWithEmailMutation,
}
