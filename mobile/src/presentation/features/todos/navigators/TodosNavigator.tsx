import { createStackNavigator } from "@react-navigation/stack"
import { Routes } from "@presentation/routes/routes"
import BoardsScreen from "../screens/BoardsScreen/BoardsScreen"

const TodosStack = createStackNavigator()

function TodosNavigator() {
  return (
    <TodosStack.Navigator
      initialRouteName={Routes.TodosBoardsScreen}
      screenOptions={{ headerShown: false }}
    >
      <TodosStack.Screen name={Routes.TodosBoardsScreen} component={BoardsScreen} />
    </TodosStack.Navigator>
  )
}

export default TodosNavigator
