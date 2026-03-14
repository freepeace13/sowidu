import {
  createStackNavigator,
  TransitionSpecs,
  CardStyleInterpolators,
  HeaderStyleInterpolators,
} from "@react-navigation/stack"

import GalleryNavigator from "./BottomTabNavigator"
import { ReadDocumentScreen, ImagePreviewScreen, WatchVideoScreen } from "../components"
import { RouteNames } from "../constants"

const Stack = createStackNavigator()

export default function Navigator() {
  return (
    <Stack.Navigator
      initialRouteName={RouteNames.GalleryNavigator}
      screenOptions={{ headerShown: false }}
    >
      <Stack.Screen name={RouteNames.GalleryNavigator} component={GalleryNavigator} />
      <Stack.Group
        screenOptions={{
          cardStyle: {
            backgroundColor: "#000",
          },
          transitionSpec: {
            open: TransitionSpecs.TransitionIOSSpec,
            close: TransitionSpecs.TransitionIOSSpec,
          },
          headerStyleInterpolator: HeaderStyleInterpolators.forFade,
          cardStyleInterpolator: CardStyleInterpolators.forVerticalIOS,
        }}
      >
        <Stack.Screen name={RouteNames.ReadDocument} component={ReadDocumentScreen} />
        <Stack.Screen name={RouteNames.ImagePreview} component={ImagePreviewScreen} />
        <Stack.Screen name={RouteNames.WatchVideo} component={WatchVideoScreen} />
      </Stack.Group>
    </Stack.Navigator>
  )
}
