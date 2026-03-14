import React from "react"
import { PaperProvider } from "react-native-paper"

import { useThemeConfig } from "./useThemeConfig"

export default function ThemeProvider({ children }) {
  const themeConfig = useThemeConfig()
  return (
    <PaperProvider theme={themeConfig} settings={{ rippleEffectEnabled: true }}>
      {children}
    </PaperProvider>
  )
}
