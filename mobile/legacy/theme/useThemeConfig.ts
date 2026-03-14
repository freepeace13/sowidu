import theme from "./base"
import { useColorMode } from "./useColorMode"

export const getTheme = (mode: "dark" | "light") => theme[mode]

export function useThemeConfig() {
  const mode = useColorMode()
  return theme[mode]
}
