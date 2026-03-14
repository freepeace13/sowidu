import { Nullable } from "@domain/shared/types"
import { createAction, createSlice, isAnyOf } from "@reduxjs/toolkit"
import { loginMatchFulfilled, logoutMatchFulfilled, registerMatchFulfilled } from "../user/userApi"
// import { persistReducer } from "redux-persist"
// import AsyncStorage from "@react-native-async-storage/async-storage"
import { RootState } from "@presentation/app/store"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { persistReducer } from "redux-persist"

type AccountState = {
  accessToken: Nullable<string>
}

const initialState: AccountState = {
  accessToken: null,
}

export const accountSlice = createSlice({
  name: "account",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addMatcher(logoutMatchFulfilled, (state) => {
        state.accessToken = null
      })
      .addMatcher(
        isAnyOf(loginMatchFulfilled, registerMatchFulfilled, tokenReceived),
        (state, { payload }) => {
          state.accessToken = payload
        }
      )
  },
})

export const accountReducer = persistReducer(
  {
    key: "account",
    storage: AsyncStorage,
    whitelist: ["accessToken"],
  },
  accountSlice.reducer
)

export const selectAccessToken = (state: RootState) => state.account.accessToken

export const tokenReceived = createAction<string>("account/tokenReceived")
