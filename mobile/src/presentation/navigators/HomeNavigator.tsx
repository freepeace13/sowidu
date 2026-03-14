import HomeScreen from "@presentation/features/home/screens/HomeScreen/HomeScreen"
import NotificationScreen from "@presentation/features/notification/screens/NotificationScreen/NotificationScreen"
import { Routes } from "@presentation/routes/routes"
import React from "react"
import { createMaterialBottomTabNavigator } from "react-native-paper/react-navigation"

const BottomTab = createMaterialBottomTabNavigator()

function HomeNavigator() {
  return (
    <BottomTab.Navigator
      labeled
      activeColor="#006686"
      sceneAnimationType="shifting"
      initialRouteName={Routes.HomeScreen}
    >
      <BottomTab.Screen
        name={Routes.HomeScreen}
        component={HomeScreen}
        options={{ title: "Home", tabBarIcon: "home" }}
      />
      <BottomTab.Screen
        name={Routes.NotificationScreen}
        component={NotificationScreen}
        options={{ title: "Notifications", tabBarIcon: "bell", tabBarBadge: 3 }}
      />
    </BottomTab.Navigator>
  )
}

export default HomeNavigator
