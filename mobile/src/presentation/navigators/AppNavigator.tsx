import { NavigationContainer } from "@react-navigation/native"
import React from "react"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import DrawerNavigator from "./DrawerNavigator/DrawerNavigator"
import GuestNavigator from "./GuestNavigator"

function AppNavigator() {
  const { isGuest } = useAccount()

  const onStateChange = () => {
    // Do navigation state change handlers
  }

  return (
    <NavigationContainer onStateChange={onStateChange}>
      {isGuest ? <GuestNavigator /> : <DrawerNavigator />}
    </NavigationContainer>
  )
}

export default AppNavigator
