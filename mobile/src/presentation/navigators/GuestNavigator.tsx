import LoginScreen from "@presentation/features/user/screens/LoginScreen/LoginScreen"
import RegisterScreen from "@presentation/features/user/screens/RegisterScreen/RegisterScreen"
import { createStackNavigator } from "@react-navigation/stack"
import React from "react"

import { Routes } from "../routes/routes"
import IntroductionScreen from "@presentation/features/user/screens/IntroductionScreen/IntroductionScreen"

const GuestStack = createStackNavigator()

function GuestNavigator() {
  return (
    <GuestStack.Navigator
      initialRouteName={Routes.IntroductionScreen}
      screenOptions={{ headerShown: false }}
    >
      <GuestStack.Screen name={Routes.IntroductionScreen} component={IntroductionScreen} />
      <GuestStack.Screen name={Routes.LoginScreen} component={LoginScreen} />
      <GuestStack.Screen name={Routes.RegisterScreen} component={RegisterScreen} />
    </GuestStack.Navigator>
  )
}

export default GuestNavigator
