import { createStackNavigator } from "@react-navigation/stack"
import { Routes } from "@presentation/routes/routes"
import DeliveriesScreen from "../screens/DeliveriesScreen/DeliveriesScreen"

const DeliveriesStack = createStackNavigator()

function DeliveriesNavigator() {
  return (
    <DeliveriesStack.Navigator
      initialRouteName={Routes.DeliveriesDeliveriesScreen}
      screenOptions={{ headerShown: false }}
    >
      <DeliveriesStack.Screen
        name={Routes.DeliveriesDeliveriesScreen}
        component={DeliveriesScreen}
      />
    </DeliveriesStack.Navigator>
  )
}

export default DeliveriesNavigator
