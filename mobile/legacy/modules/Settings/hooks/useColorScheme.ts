import { useAppSelector, useAppDispatch } from "core-module/store/hooks"

import { selectColorScheme, settingsSlice } from "../store"

export function useColorScheme() {
  const dispatch = useAppDispatch()
  const colorScheme = useAppSelector(selectColorScheme)

  const setColorScheme = (value) => {
    dispatch(settingsSlice.actions.setColorScheme(value))
  }

  return [colorScheme, setColorScheme]
}
