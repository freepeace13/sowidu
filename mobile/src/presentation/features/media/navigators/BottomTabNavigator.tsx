import { Routes } from "@presentation/routes/routes"
import { createMaterialBottomTabNavigator } from "react-native-paper/react-navigation"

import FilesScreen from "../screens/FilesScreen/FilesScreen"
import SharedFilesScreen from "../screens/SharedFilesScreen/SharedFilesScreen"
import TrashScreen from "../screens/TrashScreen/TrashScreen"

const BottomTab = createMaterialBottomTabNavigator()

function BottomTabNavigator() {
  return (
    <BottomTab.Navigator initialRouteName={Routes.FilesScreen}>
      <BottomTab.Screen
        name={Routes.FilesScreen}
        component={FilesScreen}
        options={{
          title: "Files",
          tabBarIcon: "folder-open",
        }}
      />
      <BottomTab.Screen
        name={Routes.SharedFilesScreen}
        component={SharedFilesScreen}
        options={{
          title: "Shared",
          tabBarIcon: "folder-account",
        }}
      />
      <BottomTab.Screen
        name={Routes.TrashScreen}
        component={TrashScreen}
        options={{
          title: "Trash",
          tabBarIcon: "trash-can",
        }}
      />
    </BottomTab.Navigator>
  )
}

export default BottomTabNavigator
