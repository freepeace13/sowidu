import { createDrawerNavigator, DrawerContentScrollView } from "@react-navigation/drawer"
import { Constants as CoreConstants, AccountSettingsScreen } from "core-module"
import { Navigator as MediaNavigator, Constants as MediaConstants } from "media-module"
import { DarkModeScreen } from "settings-module"
import { SwitchTeamNavigator } from "teams-module"

import DrawerContent from "./DrawerContent"
import BottomTabNavigator from "../BottomTabNavigator"

const Drawer = createDrawerNavigator()

export default function DrawerNavigator() {
  return (
    <Drawer.Navigator
      initialRouteName={CoreConstants.RouteNames.HomeNavigator}
      screenOptions={{ headerShown: false }}
      drawerContent={(props) => <DrawerContent {...props} />}
    >
      <Drawer.Screen name={CoreConstants.RouteNames.HomeNavigator} component={BottomTabNavigator} />
      <Drawer.Screen name={MediaConstants.RouteNames.MediaNavigator} component={MediaNavigator} />
      <Drawer.Screen
        name="AccountSettingsScreen"
        component={AccountSettingsScreen}
        options={{ title: "Account Settings" }}
      />
      <Drawer.Screen
        name="SwitchTeamNavigator"
        component={SwitchTeamNavigator}
        options={{
          title: "Switch Account",
          headerShown: false,
        }}
      />
      <Drawer.Screen name="DarkModeScreen" component={DarkModeScreen} />
    </Drawer.Navigator>
  )
}
