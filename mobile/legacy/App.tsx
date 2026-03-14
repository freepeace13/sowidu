import { useFonts, Roboto_400Regular, Roboto_500Medium } from "@expo-google-fonts/roboto"
import * as NavigationBar from "expo-navigation-bar"
import * as SplashScreen from "expo-splash-screen"
import { GestureHandlerRootView } from "react-native-gesture-handler"
import { Provider as StoreProvider } from "react-redux"
import { PersistGate } from "redux-persist/integration/react"
import { AnimatedSplashScreen } from "ui-module"

import { AppNavigator } from "./navigator"
import { store, persistor } from "./store/configureStore"
import ThemeProvider from "./theme"

import "./Sentry"

// Instruct SplashScreen not to hide yet, we want to do this manually
SplashScreen.preventAutoHideAsync().catch(() => undefined)

NavigationBar.setVisibilityAsync("visible")
NavigationBar.setBehaviorAsync("inset-touch")

function App() {
  const [fontsLoaded] = useFonts({
    Roboto_400Regular,
    Roboto_500Medium,
  })

  if (!fontsLoaded) {
    return null
  }

  return (
    <AnimatedSplashScreen image={require("../assets/images/splash-white.png")}>
      <StoreProvider store={store}>
        <PersistGate persistor={persistor}>
          <GestureHandlerRootView style={{ flex: 1 }}>
            <ThemeProvider>
              <AppNavigator />
            </ThemeProvider>
          </GestureHandlerRootView>
        </PersistGate>
      </StoreProvider>
    </AnimatedSplashScreen>
  )
}

export default App
