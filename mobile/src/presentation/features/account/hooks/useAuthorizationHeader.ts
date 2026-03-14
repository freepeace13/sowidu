import { useAccount } from "./useAccount"

export const useAuthorizationHeader = (obj: Record<string, any>) => {
  const { accessToken } = useAccount()
  if (accessToken) {
    Object.assign(obj, {
      Authorization: `Bearer ${accessToken}`,
    })
  }
  return obj
}
