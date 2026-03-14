import { RootState } from "@presentation/app/store"
import { createEntityAdapter, createSlice } from "@reduxjs/toolkit"
import { Team } from "@domain/teams/team/Team"

const chatsAdapter = createEntityAdapter<Team>({
  sortComparer: (a, b) => a.name.localeCompare(b.name),
})

interface ChatsState extends ReturnType<typeof chatsAdapter.getInitialState> {
  loading: boolean
  error: any
}

const initialState: ChatsState = chatsAdapter.getInitialState({
  loading: false,
  error: null,
})

export const chatsSlice = createSlice({
  name: "chats",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    //
  },
})

export const teamsReducer = chatsSlice.reducer
