import { createStackNavigator } from "@react-navigation/stack"
import { Routes } from "@presentation/routes/routes"

import BottomTabNavigator from "./BottomTabNavigator"
import ChatScreen from "../screens/ChatScreen/ChatScreen"

const ChatsStack = createStackNavigator()

function ChatsNavigator() {
  return (
    <ChatsStack.Navigator screenOptions={{ headerShown: false }}>
      <ChatsStack.Screen name={Routes.ChatsBottomTabNavigator} component={BottomTabNavigator} />
      <ChatsStack.Screen name={Routes.ChatsChatScreen} component={ChatScreen} />
    </ChatsStack.Navigator>
  )
}

export default ChatsNavigator
