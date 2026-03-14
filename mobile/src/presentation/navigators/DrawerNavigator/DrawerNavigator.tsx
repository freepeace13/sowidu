import { Routes } from "@presentation/routes/routes"
import { DrawerNavigationOptions, createDrawerNavigator } from "@react-navigation/drawer"

import DrawerContent from "./DrawerContent"
import MediaNavigator from "@presentation/features/media/navigators/MediaNavigator"
import HomeNavigator from "@presentation/navigators/HomeNavigator"
import TeamsNavigator from "@presentation/features/teams/navigators/TeamsNavigator"
import AccountNavigator from "@presentation/features/account/navigators/AccountNavigator"
import { useTheme } from "react-native-paper"
import React from "react"
import { StatusBar } from "expo-status-bar"
import ChatsNavigator from "@presentation/features/chats/navigators/ChatsNavigator"
import TodosNavigator from "@presentation/features/todos/navigators/TodosNavigator"
import OrdersNavigator from "@presentation/features/orders/navigators/OrdersNavigator"
import DeliveriesNavigator from "@presentation/features/deliveries/navigators/DeliveriesNavigator"
import AddressbookNavigator from "@presentation/features/addressbook/navigators/AddressbookNavigator"

const DrawerStack = createDrawerNavigator()

function DrawerNavigator() {
  const { colors } = useTheme()
  return (
    <>
      <StatusBar translucent={false} backgroundColor={colors.primary} />
      <DrawerStack.Navigator
        initialRouteName={Routes.HomeNavigator}
        drawerContent={(props) => <DrawerContent {...props} />}
        screenOptions={{
          drawerStyle: {
            backgroundColor: colors.background,
          },
          drawerType: "slide",
          headerShown: false,
        }}
      >
        <DrawerStack.Screen name={Routes.HomeNavigator} component={HomeNavigator} />
        <DrawerStack.Screen name={Routes.TeamsNavigator} component={TeamsNavigator} />
        <DrawerStack.Screen name={Routes.MediaNavigator} component={MediaNavigator} />
        <DrawerStack.Screen name={Routes.AccountNavigator} component={AccountNavigator} />
        <DrawerStack.Screen name={Routes.ChatsNavigator} component={ChatsNavigator} />
        <DrawerStack.Screen name={Routes.TodosNavigator} component={TodosNavigator} />
        <DrawerStack.Screen name={Routes.AddressbookNavigator} component={AddressbookNavigator} />
        <DrawerStack.Screen name={Routes.OrdersNavigator} component={OrdersNavigator} />
        <DrawerStack.Screen name={Routes.DeliveriesNavigator} component={DeliveriesNavigator} />
      </DrawerStack.Navigator>
    </>
  )
}

export default DrawerNavigator
