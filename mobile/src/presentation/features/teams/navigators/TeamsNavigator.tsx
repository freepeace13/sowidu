import { Routes } from "@presentation/routes/routes"
import { createStackNavigator } from "@react-navigation/stack"

import CreateTeamScreen from "../screens/CreateTeamScreen/CreateTeamScreen"
import SwitchTeamScreen from "../screens/SwitchTeamScreen/SwitchTeamScreen"
import TeamsScreen from "../screens/TeamsScreen/TeamsScreen"

const TeamsStack = createStackNavigator()

function TeamsNavigator() {
  return (
    <TeamsStack.Navigator screenOptions={{ headerShown: false }}>
      <TeamsStack.Screen name={Routes.SwitchTeamScreen} component={SwitchTeamScreen} />
      <TeamsStack.Screen name={Routes.TeamsScreen} component={TeamsScreen} />
      <TeamsStack.Screen
        name={Routes.CreateTeamScreen}
        component={CreateTeamScreen}
        options={{
          presentation: "modal",
          gestureEnabled: true,
          gestureVelocityImpact: 1,
        }}
      />
    </TeamsStack.Navigator>
  )
}

export default TeamsNavigator
