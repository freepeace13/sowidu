import { RootState } from "@presentation/app/store"
import { createEntityAdapter, createSlice } from "@reduxjs/toolkit"
import { createTeam, getTeams } from "./teamsApi"
import { Team } from "@domain/teams/team/Team"

const teamsAdapter = createEntityAdapter<Team>({
  sortComparer: (a, b) => a.name.localeCompare(b.name),
})

interface TeamsState extends ReturnType<typeof teamsAdapter.getInitialState> {
  loading: boolean
  error: any
}

const initialState: TeamsState = teamsAdapter.getInitialState({
  loading: false,
  error: null,
})

export const teamsSlice = createSlice({
  name: "teams",
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addMatcher(createTeam.matchFulfilled, teamsAdapter.addOne)
      .addMatcher(getTeams.matchFulfilled, (state, { payload }) => {
        teamsAdapter.setAll(state, payload)
        state.loading = false
      })
      .addMatcher(getTeams.matchPending, (state) => {
        state.loading = true
      })
      .addMatcher(getTeams.matchRejected, (state, { payload }) => {
        state.loading = false
        state.error = payload
      })
  },
})

export const teamsReducer = teamsSlice.reducer

export const teamsSelectors = teamsAdapter.getSelectors((state: RootState) => state.teams)

export const selectTeamById = (id: number) => (state: RootState) =>
  teamsSelectors.selectById(state, id)

export const selectIsGetTeamsLoading = (state: RootState) => state.teams.loading
