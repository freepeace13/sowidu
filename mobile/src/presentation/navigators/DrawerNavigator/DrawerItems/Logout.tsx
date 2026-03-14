import { useLogoutMutation } from "@presentation/features/user/userApi"
import { DrawerItem } from "@react-navigation/drawer"
import { Icon } from "react-native-paper"

function Logout() {
  const [logout] = useLogoutMutation()
  return (
    <DrawerItem
      icon={(props) => <Icon {...props} source="logout" />}
      label="Logout"
      onPress={logout}
    />
  )
}

export default Logout
