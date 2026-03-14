import { createStackNavigator } from "@react-navigation/stack"

import { SwitchTeamScreen, CreateTeamScreen } from "../components"

const SwitchTeamStack = createStackNavigator()

export default function SwitchTeamNavigator() {
  return (
    <SwitchTeamStack.Navigator
      initialRouteName="SwitchTeamScreen"
      screenOptions={{ headerShown: false }}
    >
      <SwitchTeamStack.Screen name="SwitchTeamScreen" component={SwitchTeamScreen} />
      <SwitchTeamStack.Screen name="CreateTeamScreen" component={CreateTeamScreen} />
    </SwitchTeamStack.Navigator>
  )
}
