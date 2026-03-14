import { tokenReceived } from "@presentation/features/account/accountSlice"
import { getCurrentUser } from "@presentation/features/user/userApi"
import { createListenerMiddleware } from "@reduxjs/toolkit"

const refetchCurrentUserListener = createListenerMiddleware()

refetchCurrentUserListener.startListening({
  actionCreator: tokenReceived,
  effect: ({ payload }, { dispatch }) => {
    dispatch(
      getCurrentUser.initiate(undefined, {
        subscribe: false,
        forceRefetch: true,
      })
    )
  },
})

export const refetchCurrentUserMiddleware = refetchCurrentUserListener.middleware
