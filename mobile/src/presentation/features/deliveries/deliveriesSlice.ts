import { RootState } from "@presentation/app/store"
import { createEntityAdapter, createSlice } from "@reduxjs/toolkit"
import { Team } from "@domain/teams/team/Team"

const chatAdapter = createEntityAdapter<Team>({
  sortComparer: (a, b) => a.name.localeCompare(b.name),
})

interface ChatState extends ReturnType<typeof chatAdapter.getInitialState> {
  loading: boolean
  error: any
}

const initialState: ChatState = chatAdapter.getInitialState({
  loading: false,
  error: null,
})

export const chatSlice = createSlice({
  name: "chat",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    //
  },
})

export const teamsReducer = chatSlice.reducer
