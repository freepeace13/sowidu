import { createMaterialBottomTabNavigator } from "react-native-paper/react-navigation"
import { Routes } from "@presentation/routes/routes"
import React from "react"
import SettingsScreen from "../screens/SettingsScreen/SettingsScreen"
import PeopleScreen from "../screens/PeopleScreen/PeopleScreen"
import ConversationsScreen from "../screens/ConversationsScreen/ConversationsScreen"

const BottomTab = createMaterialBottomTabNavigator()

function BottomTabNavigator() {
  return (
    <BottomTab.Navigator
      labeled
      activeColor="#006686"
      sceneAnimationType="shifting"
      initialRouteName={Routes.ChatsConversationsScreen}
    >
      <BottomTab.Screen
        name={Routes.ChatsConversationsScreen}
        component={ConversationsScreen}
        options={{ title: "Chat", tabBarIcon: "message", tabBarBadge: 3 }}
      />
      <BottomTab.Screen
        name={Routes.ChatsPeopleScreen}
        component={PeopleScreen}
        options={{ title: "People", tabBarIcon: "account-supervisor" }}
      />
      <BottomTab.Screen
        name={Routes.ChatsSettigsScreen}
        component={SettingsScreen}
        options={{ title: "Settings", tabBarIcon: "cog" }}
      />
    </BottomTab.Navigator>
  )
}

export default BottomTabNavigator
