import { createContext, FunctionComponent, ReactNode, useEffect, useMemo } from "react"
import { User } from "@domain/user/User"
import { useAppDispatch, useAppSelector } from "@presentation/app/store/hooks"
import { selectAccessToken, tokenReceived } from "../accountSlice"
import { useGetCurrentUserQuery } from "@presentation/features/user/userApi"
import ScreenLoading from "@presentation/components/ScreenLoading/ScreenLoading"
import { authService } from "@application/main"
import type { CurrentTeam } from "@domain/teams/team/Team"

export interface AccountContextType {
  user?: User
  accessToken?: string
  currentTeam?: CurrentTeam | null
  isGuest: boolean
}

export const AccountContext = createContext<AccountContextType>({} as AccountContextType)

interface AccountProviderProps {
  children: ReactNode
}

const AccountProvider: FunctionComponent<AccountProviderProps> = ({ children }) => {
  const dispatch = useAppDispatch()
  const accessToken = useAppSelector(selectAccessToken)
  const { data: user, isLoading } = useGetCurrentUserQuery(undefined, {
    skip: !accessToken,
  })
  const isGuest = useMemo(() => typeof accessToken !== "string", [accessToken])
  const currentTeam = useMemo(() => user?.currentTeam, [user])

  useEffect(() => {
    async function retrieveToken() {
      const token = await authService.retrieveToken()
      if (token) {
        dispatch(tokenReceived(token))
      }
    }

    if (!accessToken) {
      retrieveToken()
    }
  }, [accessToken, dispatch])

  return (
    <AccountContext.Provider
      value={{
        isGuest,
        currentTeam,
        accessToken,
        user,
      }}
    >
      <ScreenLoading isLoading={isLoading}>{children}</ScreenLoading>
    </AccountContext.Provider>
  )
}

export default AccountProvider
