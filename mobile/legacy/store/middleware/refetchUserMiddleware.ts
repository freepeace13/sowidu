import { createListenerMiddleware } from "@reduxjs/toolkit"
import { Api as AuthApi } from "auth-module"
import { Api as TeamsApi } from "teams-module"

const refetchUserListenerMiddleware = createListenerMiddleware()

refetchUserListenerMiddleware.startListening({
  matcher: TeamsApi.switchTeam.matchFulfilled,
  effect: (_, listenerApi) => {
    listenerApi.dispatch(
      AuthApi.getUserInfo.initiate(undefined, {
        subscribe: false,
        forceRefetch: true,
      }),
    )
  },
})

export const refetchUserMiddleware = refetchUserListenerMiddleware.middleware
