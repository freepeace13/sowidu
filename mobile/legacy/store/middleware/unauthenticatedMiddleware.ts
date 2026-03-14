import { isRejectedWithValue } from "@reduxjs/toolkit"
import { Api as CoreApi } from "core-module"

import { resetState } from "../actions/resetState"

export function unauthenticatedMiddleware({ dispatch }) {
  return (next) => (action) => {
    if (isRejectedWithValue(action) && action.payload.status === 401) {
      // dispatch(CoreApi.util.resetApiState())
      dispatch(resetState())
    }

    return next(action)
  }
}
