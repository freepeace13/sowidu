import { useAppSelector } from "core-module"

import { Api as AuthApi } from "../services"
import { selectAccessToken, selectUserInfo } from "../store"
import { UserInfo } from "../types"

export { useLoginForm } from "./useLoginForm"
export { useRegisterForm } from "./useRegisterForm"

export const useAuthToken = (): string => {
  return useAppSelector(selectAccessToken)
}

export const useUserInfo = (): UserInfo => {
  const userInfo = useAppSelector(selectUserInfo)
  const userQueryState = AuthApi.getUserInfo.useQueryState()
  return userQueryState.data || userInfo
}

export const useIsAuthenticated = (): boolean => {
  return !!useAppSelector(selectAccessToken)
}
