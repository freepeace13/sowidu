import AsyncStorage from "@react-native-async-storage/async-storage"
import { createSlice } from "@reduxjs/toolkit"
import { persistReducer } from "redux-persist"

import { Api as AuthApi } from "../services"
import { UserInfo } from "../types"

interface AuthState {
  accessToken: string
  currentUser?: UserInfo | undefined
}

const initialState: AuthState = {
  accessToken: null,
  currentUser: null,
}

export const authSlice = createSlice({
  name: "auth",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder.addMatcher(AuthApi.logout.matchFulfilled, (state) => {
      state.accessToken = null
    })
    builder.addMatcher(AuthApi.loginWithEmail.matchFulfilled, (state, { payload }) => {
      state.accessToken = payload
    })
    builder.addMatcher(AuthApi.getUserInfo.matchFulfilled, (state, { payload }) => {
      state.currentUser = payload
    })
  },
})

export const authReducer = persistReducer(
  {
    key: "@user",
    storage: AsyncStorage,
    whitelist: ["accessToken", "currentUser"],
  },
  authSlice.reducer,
)
