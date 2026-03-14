import { authService, userService } from "@application/main"
import { UpdateUserAvatarData, UpdateUserInfoData } from "@domain/user/ProfileRepository"
import { User } from "@domain/user/User"
import { UserLoginData, UserRegisterData } from "@domain/user/UserRepository"
import { RootState } from "@presentation/app/store"
import { sharedApi } from "@presentation/features/shared/api"
import { createSelector } from "@reduxjs/toolkit"
import * as Device from "expo-device"

const userApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    login: build.mutation<string, Omit<UserLoginData, "device">>({
      queryFn: (args) => {
        const device = Device.deviceName as string
        return authService
          .requestToken({ ...args, device })
          .then((token) => ({ data: token }))
          .catch((error) => ({ error }))
      },
    }),

    logout: build.mutation<boolean, void>({
      queryFn: () => {
        return authService
          .revokeToken()
          .then(() => ({ data: true }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted(_, { dispatch, queryFulfilled }) {
        try {
          await queryFulfilled
          dispatch(userApi.util.resetApiState())
        } catch {}
      },
    }),

    register: build.mutation<string, UserRegisterData>({
      queryFn: (args) => {
        return userService
          .registerUser(args)
          .then((token) => ({ data: token }))
          .catch((error) => ({ error }))
      },
    }),

    getCurrentUser: build.query<User, void>({
      queryFn: () => {
        return authService
          .tokenUser()
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      providesTags: ["User"],
    }),

    updateUserAvatar: build.mutation<string, UpdateUserAvatarData>({
      queryFn: (args) => {
        return userService
          .changeUserAvatar(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted(_, { dispatch, queryFulfilled }) {
        /** @todo create listener middleware and update the getCurrentUser cache there */
        try {
          const { data: uri } = await queryFulfilled
          dispatch(
            userApi.util.updateQueryData("getCurrentUser" as const, undefined, (draft) => {
              Object.assign(draft, { avatar: uri })
            })
          )
        } catch {}
      },
    }),

    updateUserInfo: build.mutation<User, UpdateUserInfoData>({
      queryFn: (args) => {
        return userService
          .updateUserInfo(args)
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
      async onQueryStarted(_, { dispatch, queryFulfilled }) {
        /** @todo create listener middleware and update the getCurrentUser cache there */
        try {
          const { data } = await queryFulfilled
          dispatch(
            userApi.util.updateQueryData("getCurrentUser" as const, undefined, (draft) => {
              Object.assign(draft, { ...data })
            })
          )
        } catch {}
      },
    }),
  }),
})

export const selectGetCurrentUserQueryState = createSelector(
  (state: RootState) => userApi.endpoints.getCurrentUser.select()(state),
  (response) => response
)

export const selectCurrentUser = createSelector(
  (state: RootState) => selectGetCurrentUserQueryState(state),
  (response) => response.data
)

export const loginMatchFulfilled = userApi.endpoints.login.matchFulfilled
export const registerMatchFulfilled = userApi.endpoints.register.matchFulfilled
export const logoutMatchFulfilled = userApi.endpoints.logout.matchFulfilled

export const { getCurrentUser } = userApi.endpoints

export const resetUserApiState = userApi.util.resetApiState
export const updateUserApiQueryData = userApi.util.updateQueryData

export const {
  useGetCurrentUserQuery,
  useLoginMutation,
  useRegisterMutation,
  useLogoutMutation,
  useUpdateUserAvatarMutation,
  useUpdateUserInfoMutation,
} = userApi
