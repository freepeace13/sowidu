export const selectAccessToken = (state) => {
  return state.auth.accessToken
}

export const selectUserInfo = (state) => {
  return state.auth.currentUser
}
