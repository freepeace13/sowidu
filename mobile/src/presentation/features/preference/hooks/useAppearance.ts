import { useAppDispatch, useAppSelector } from "@presentation/app/store/hooks"
import { useCallback } from "react"
import { useColorScheme } from "react-native"

import { selectTheme, changeTheme, Theme } from "../preferenceSlice"

interface UseColorModeResult {
  theme: "dark" | "light"
  isDark: boolean
  changeTheme: (value: Theme) => void
}

export const useColorMode = (): UseColorModeResult => {
  const dispatch = useAppDispatch()
  const defaultTheme = useColorScheme()
  const preferredTheme = useAppSelector(selectTheme)

  const themeColor = preferredTheme === "auto" ? defaultTheme : preferredTheme

  const setPreferredTheme = useCallback(
    (theme: Theme) => {
      dispatch(changeTheme(theme))
    },
    [dispatch]
  )

  return {
    theme: themeColor || "light",
    isDark: themeColor === "dark",
    changeTheme: setPreferredTheme,
  }
}
