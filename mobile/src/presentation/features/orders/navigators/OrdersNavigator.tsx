import { createStackNavigator } from "@react-navigation/stack"
import { Routes } from "@presentation/routes/routes"
import OrdersScreen from "../screens/OrdersScreen/OrdersScreen"

const OrdersStack = createStackNavigator()

function OrdersNavigator() {
  return (
    <OrdersStack.Navigator
      initialRouteName={Routes.OrdersOrdersScreen}
      screenOptions={{ headerShown: false }}
    >
      <OrdersStack.Screen name={Routes.OrdersOrdersScreen} component={OrdersScreen} />
    </OrdersStack.Navigator>
  )
}

export default OrdersNavigator
