import { createStackNavigator } from "@react-navigation/stack"
import { Routes } from "@presentation/routes/routes"
import PeopleScreen from "../screens/PeopleScreen/PeopleScreen"
import ProfileScreen from "../screens/ProfileScreen/ProfileScreen"

const AddressbookStack = createStackNavigator()

function AddressbookNavigator() {
  return (
    <AddressbookStack.Navigator
      initialRouteName={Routes.AddressbookPeopleScreen}
      screenOptions={{ headerShown: false }}
    >
      <AddressbookStack.Screen name={Routes.AddressbookPeopleScreen} component={PeopleScreen} />
      <AddressbookStack.Screen name={Routes.AddressBookProfileScreen} component={ProfileScreen} />
    </AddressbookStack.Navigator>
  )
}

export default AddressbookNavigator
