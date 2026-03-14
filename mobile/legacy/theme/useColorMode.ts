import { useColorScheme as useSystemColorScheme } from "react-native"
import { useColorScheme } from "settings-module"

export function useColorMode() {
  const [colorScheme] = useColorScheme()
  const systemColorScheme = useSystemColorScheme()

  return colorScheme === "auto" ? systemColorScheme : colorScheme
}
