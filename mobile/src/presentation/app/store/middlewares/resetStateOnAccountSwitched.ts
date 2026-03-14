import { createListenerMiddleware } from "@reduxjs/toolkit"
import { resetMediaApiState } from "@presentation/features/media/mediaApi"
import { updateUserApiQueryData } from "@presentation/features/user/userApi"
import { switchAccount } from "@presentation/features/account/accountApi"

const resetStateOnAccountSwitched = createListenerMiddleware()

resetStateOnAccountSwitched.startListening({
  matcher: switchAccount.matchFulfilled,
  effect: ({ payload }, { dispatch }) => {
    const resetCurrentTeam = updateUserApiQueryData(
      "getCurrentUser" as const,
      undefined,
      (draft) => {
        draft.currentTeam = payload
      }
    ) as any

    dispatch(resetCurrentTeam)
    dispatch(resetMediaApiState())
  },
})

export const resetStateOnAccountSwitchedMiddleware = resetStateOnAccountSwitched.middleware
