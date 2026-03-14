import { Appbar } from "react-native-paper"
import { createMaterialBottomTabNavigator } from "react-native-paper/react-navigation"

import { FilesScreen, TrashScreen } from "../components/Gallery"
import * as MediaConstants from "../constants"

const BottomTab = createMaterialBottomTabNavigator()

export default function BottomTabNavigator() {
  return (
    <BottomTab.Navigator initialRouteName={MediaConstants.RouteNames.Gallery.Files}>
      <BottomTab.Screen
        name={MediaConstants.RouteNames.Gallery.Files}
        component={FilesScreen}
        options={{
          tabBarIcon: "folder-open",
          tabBarLabel: "Files",
        }}
      />
      <BottomTab.Screen
        name={MediaConstants.RouteNames.Gallery.Trash}
        component={TrashScreen}
        options={{
          tabBarIcon: "trash-can",
          tabBarLabel: "Trash",
        }}
      />
    </BottomTab.Navigator>
  )
}
