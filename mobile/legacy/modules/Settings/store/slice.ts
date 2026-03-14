import AsyncStorage from "@react-native-async-storage/async-storage"
import { createSlice } from "@reduxjs/toolkit"
import { persistReducer } from "redux-persist"

import * as reducers from "./reducers"

const initialState = {
  colorScheme: "auto",
}

export const settingsSlice = createSlice({
  name: "settings",
  initialState,
  reducers,
})

export const settingsReducer = persistReducer(
  {
    key: "@settings",
    storage: AsyncStorage,
    whitelist: ["colorScheme"],
  },
  settingsSlice.reducer,
)
