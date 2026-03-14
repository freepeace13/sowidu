import { NavigationContainer } from "@react-navigation/native"
import { Authenticated } from "auth-module"
import React from "react"

import DrawerNavigator from "./DrawerNavigator"

export default function AppNavigator() {
  const onStateChange = () => {
    // Do navigation state change handlers
  }
  return (
    <NavigationContainer onStateChange={onStateChange}>
      <Authenticated>
        <DrawerNavigator />
      </Authenticated>
    </NavigationContainer>
  )
}
