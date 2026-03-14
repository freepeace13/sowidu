import { Routes } from "@presentation/routes/routes"
import { createStackNavigator } from "@react-navigation/stack"

import SettingsScreen from "../screens/Settings/SettingsScreen"
import TeamSettingsNavigator from "@presentation/features/teams/navigators/TeamSettingsNavigator"
import ProfileScreen from "../screens/Profile/ProfileScreen"

const AccountStack = createStackNavigator()

function AccountNavigator() {
  return (
    <AccountStack.Navigator
      initialRouteName={Routes.AccountSettingsScreen}
      screenOptions={{ headerShown: false }}
    >
      <AccountStack.Screen
        name={Routes.AccountSettingsScreen}
        component={SettingsScreen}
        options={{ freezeOnBlur: true }}
      />
      <AccountStack.Screen
        name={Routes.AccountEditProfileScreen}
        component={ProfileScreen}
        options={{ gestureEnabled: false }}
      />
      <AccountStack.Screen name={Routes.TeamSettingsNavigator} component={TeamSettingsNavigator} />
    </AccountStack.Navigator>
  )
}

export default AccountNavigator
