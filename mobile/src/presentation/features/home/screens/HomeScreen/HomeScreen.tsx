import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import HomeHeader from "@presentation/features/home/components/HomeHeader/HomeHeader"
import HomeLinks from "@presentation/features/home/components/HomeLinks/HomeLinks"
import { Routes } from "@presentation/routes/routes"
import { useNavigation, DrawerActions, ParamListBase } from "@react-navigation/native"
import { View } from "react-native"

import Style from "./HomeScreenStyle"
import { DrawerNavigationProp } from "@react-navigation/drawer"

function HomeScreen() {
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()
  const onMenuPress = () => {
    navigation.dispatch(DrawerActions.openDrawer())
  }
  return (
    <ScreenContainer>
      <HomeHeader onMenuPress={onMenuPress} />
      <View style={Style.content}>
        <HomeLinks
          onAddressBookPress={() => navigation.navigate(Routes.AddressbookNavigator)}
          onChatsPress={() => navigation.navigate(Routes.ChatsNavigator)}
          onDeliveryTicketsPress={() => navigation.navigate(Routes.DeliveriesNavigator)}
          onMediaPress={() => navigation.navigate(Routes.MediaNavigator)}
          onOrdersPress={() => navigation.navigate(Routes.OrdersNavigator)}
          onTodoPress={() => navigation.navigate(Routes.TodosNavigator)}
        />
      </View>
    </ScreenContainer>
  )
}

export default HomeScreen
