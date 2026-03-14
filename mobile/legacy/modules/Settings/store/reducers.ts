export const setColorScheme = (state, { payload }) => {
  if (!["auto", "light", "dark"].includes(payload)) {
    throw new Error(`Invalid color scheme value: ${payload}`)
  }

  state.colorScheme = payload
}
