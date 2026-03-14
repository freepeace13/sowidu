import { RootState } from "@presentation/app/store"
import { PayloadAction, createSlice } from "@reduxjs/toolkit"

export type Theme = "auto" | "dark" | "light"

export interface PreferenceState {
  theme: Theme
}

const initialState: PreferenceState = {
  theme: "auto",
}

export const preferenceSlice = createSlice({
  name: "preference",
  initialState,
  reducers: {
    changeTheme: (state, { payload }: PayloadAction<Theme>) => {
      state.theme = payload
    },
  },
})

export const preferenceReducer = preferenceSlice.reducer

export const { changeTheme } = preferenceSlice.actions

export const selectTheme = (state: RootState) => state.preference.theme
