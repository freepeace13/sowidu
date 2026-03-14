import Sentry from "./Sentry"
import {
  useFonts,
  Roboto_400Regular,
  Roboto_500Medium,
  Roboto_700Bold,
} from "@expo-google-fonts/roboto"
import { BottomSheetModalProvider } from "@gorhom/bottom-sheet"
import * as SplashScreen from "expo-splash-screen"
import { GestureHandlerRootView } from "react-native-gesture-handler"
import { PaperProvider, Text, useTheme } from "react-native-paper"
import { Provider as StoreProvider } from "react-redux"
import { PersistGate } from "redux-persist/integration/react"

import { store, persistor } from "./app/store"
import theme from "./app/theme"
import AnimatedSplashScreen from "./components/AnimatedSplashScreen/AnimatedSplashScreen"
import { useColorMode } from "./features/preference/hooks/useAppearance"
import AppNavigator from "./navigators/AppNavigator"
import { SafeAreaView } from "react-native-safe-area-context"
import { FlashMessageProvider } from "./components"
import { KeyboardProvider } from "react-native-keyboard-controller"
import AccountProvider from "./features/account/contexts/AccountContext"

// Instruct SplashScreen not to hide yet, we want to do this manually
SplashScreen.preventAutoHideAsync().catch(() => undefined)

function AppContainer() {
  const { colors } = useTheme()
  const { theme: color } = useColorMode()
  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <KeyboardProvider>
        <PaperProvider theme={theme[color]} settings={{ rippleEffectEnabled: true }}>
          <BottomSheetModalProvider>
            <FlashMessageProvider>
              <SafeAreaView style={{ flex: 1, backgroundColor: colors.background }}>
                <AccountProvider>
                  <AppNavigator />
                </AccountProvider>
              </SafeAreaView>
            </FlashMessageProvider>
          </BottomSheetModalProvider>
        </PaperProvider>
      </KeyboardProvider>
    </GestureHandlerRootView>
  )
}

function App() {
  const [fontsLoaded] = useFonts({
    Roboto_400Regular,
    Roboto_500Medium,
    Roboto_700Bold,
  })

  if (!fontsLoaded) {
    return null
  }

  return (
    <StoreProvider store={store}>
      <PersistGate persistor={persistor} loading={<Text>Loading..</Text>}>
        <AnimatedSplashScreen image={require("../../assets/images/splash-white.png")}>
          <AppContainer />
        </AnimatedSplashScreen>
      </PersistGate>
    </StoreProvider>
  )
}

export default Sentry.wrap(App)
