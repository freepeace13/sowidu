import { Routes } from "@presentation/routes/routes"
import { createStackNavigator } from "@react-navigation/stack"

import RolesPermissionsScreen from "../screens/RolesPermissionsScreen/RolesPermissionsScreen"
import ManageTeamMembersNavigator from "./ManageTeamMembersNavigator"

const TeamSettingsStack = createStackNavigator()

function TeamSettingsNavigator() {
  return (
    <TeamSettingsStack.Navigator
      initialRouteName={Routes.TeamMembersListScreen}
      screenOptions={{ headerShown: false }}
    >
      <TeamSettingsStack.Screen
        name={Routes.ManageTeamMembersNavigator}
        component={ManageTeamMembersNavigator}
      />
      <TeamSettingsStack.Screen
        name={Routes.RolesPermissionsScreen}
        component={RolesPermissionsScreen}
      />
    </TeamSettingsStack.Navigator>
  )
}

export default TeamSettingsNavigator
