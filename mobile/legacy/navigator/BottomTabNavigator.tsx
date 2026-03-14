import { HomeScreen, NotificationScreen } from "core-module"
import React from "react"
import { createMaterialBottomTabNavigator } from "react-native-paper/react-navigation"

import * as CoreConstants from "../constants"

const BottomTab = createMaterialBottomTabNavigator()

export default function BottomTabNavigator() {
  return (
    <BottomTab.Navigator
      labeled
      activeColor="#006686"
      sceneAnimationType="shifting"
      initialRouteName={CoreConstants.RouteNames.Home}
    >
      <BottomTab.Screen
        name={CoreConstants.RouteNames.Home}
        component={HomeScreen}
        options={{ title: "Home", tabBarIcon: "home" }}
      />
      <BottomTab.Screen
        name={CoreConstants.RouteNames.Notification}
        component={NotificationScreen}
        options={{ title: "Notifications", tabBarIcon: "bell", tabBarBadge: 3 }}
      />
    </BottomTab.Navigator>
  )
}
