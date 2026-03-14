import { Routes } from "@presentation/routes/routes"
import { createStackNavigator } from "@react-navigation/stack"

import TeamMembersListScreen from "../screens/TeamMembersListScreen/TeamMembersListScreen"
import TeamInviteMemberScreen from "../screens/TeamInviteMemberScreen/TeamInviteMemberScreen"
import ManageTeamMemberScreen from "../screens/TeamMember/ManageTeamMemberScreen"

const TeamMembersStack = createStackNavigator()

function ManageTeamMembersNavigator() {
  return (
    <TeamMembersStack.Navigator
      initialRouteName={Routes.TeamMembersListScreen}
      screenOptions={{ headerShown: false }}
    >
      <TeamMembersStack.Screen
        name={Routes.TeamMembersListScreen}
        component={TeamMembersListScreen}
        options={{
          freezeOnBlur: true,
        }}
      />
      <TeamMembersStack.Screen
        name={Routes.TeamInviteMemberScreen}
        component={TeamInviteMemberScreen}
      />
      <TeamMembersStack.Screen
        name={Routes.ManageTeamMemberScreen}
        component={ManageTeamMemberScreen}
        initialParams={{ memberId: null, teamId: null }}
        options={{
          presentation: "modal",
          gestureEnabled: false,
          animationEnabled: true,
          animationTypeForReplace: "push",
          detachPreviousScreen: false,
          cardShadowEnabled: false,
          cardOverlayEnabled: false,
        }}
      />
    </TeamMembersStack.Navigator>
  )
}

export default ManageTeamMembersNavigator
