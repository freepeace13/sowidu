import { MD3LightTheme, MD3DarkTheme } from "react-native-paper"
import type { MD3Colors } from "react-native-paper/lib/typescript/types"

const light: MD3Colors = {
  ...MD3LightTheme.colors,
  primary: "#006686",
  onPrimary: "#FFF",
  primaryContainer: "#C0E8FF",
  onPrimaryContainer: "#001E2B",

  secondary: "#4D616C",
  onSecondary: "#FFF",
  secondaryContainer: "#D0E6F3",
  onSecondaryContainer: "#091E27",

  tertiary: "#006780",
  onTertiary: "#FFF",
  tertiaryContainer: "#B8EAFF",
  onTertiaryContainer: "#001F28",

  error: "#BA1A1A",
  onError: "#FFF",
  errorContainer: "#FFDAD6",
  onErrorContainer: "#410002",
}

const dark: MD3Colors = {
  ...MD3DarkTheme.colors,
  primary: "#70D2FF",
  onPrimary: "#003547",
  primaryContainer: "#004D66",
  onPrimaryContainer: "#C0E8FF",

  secondary: "#B4CAD6",
  onSecondary: "#1F333D",
  secondaryContainer: "#364954",
  onSecondaryContainer: "#D0E6F3",

  tertiary: "#5DD5FC",
  onTertiary: "#003544",
  tertiaryContainer: "#004D61",
  onTertiaryContainer: "#B8EAFF",

  error: "#FFB4AB",
  onError: "#690005",
  errorContainer: "#93000A",
  onErrorContainer: "#FFDAD6",
}

const colors = { light, dark }

export { colors }
