import { Routes } from "@presentation/routes/routes"
import { createStackNavigator } from "@react-navigation/stack"

import BottomTabNavigator from "./BottomTabNavigator"
import UploadContextProvider from "../features/uploads/containers/UploadContextProvider"
import FileScreen from "../screens/FileScreen/FileScreen"
import ShareSettingsScreen from "../screens/ShareSettingsScreen/ShareSettingsScreen"

const MediaStack = createStackNavigator()

function FilesNavigator(props: any) {
  return (
    <UploadContextProvider>
      <BottomTabNavigator {...props} />
    </UploadContextProvider>
  )
}

function MediaNavigator() {
  return (
    <MediaStack.Navigator
      initialRouteName={Routes.FilesNavigator}
      screenOptions={{ headerShown: false, gestureEnabled: false }}
    >
      <MediaStack.Screen
        name={Routes.FilesNavigator}
        component={FilesNavigator}
        options={{
          freezeOnBlur: true,
        }}
      />
      <MediaStack.Screen
        name={Routes.FileScreen}
        component={FileScreen}
        options={{
          presentation: "transparentModal",
          cardShadowEnabled: false,
          cardOverlayEnabled: false,
        }}
      />
      <MediaStack.Screen
        name={Routes.ShareSettingsScreen}
        component={ShareSettingsScreen}
        options={{ presentation: "modal" }}
      />
    </MediaStack.Navigator>
  )
}

export default MediaNavigator
