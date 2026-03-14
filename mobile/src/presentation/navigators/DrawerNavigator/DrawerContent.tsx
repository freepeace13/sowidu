import { Routes } from "@presentation/routes/routes"
import {
  DrawerContentComponentProps,
  DrawerContentScrollView,
  DrawerItem,
  useDrawerProgress,
} from "@react-navigation/drawer"
import { Divider, Icon, Text } from "react-native-paper"

import DrawerHeader from "./DrawerHeader/DrawerHeader"
import AccountSummary from "./DrawerItems/AccountSummary"
import DarkMode from "./DrawerItems/DarkMode"
import Logout from "./DrawerItems/Logout"
import Animated, { Extrapolation, interpolate, useAnimatedStyle } from "react-native-reanimated"

function DrawerContent(props: DrawerContentComponentProps) {
  const { navigate, closeDrawer } = props.navigation

  const progress = useDrawerProgress()

  const animatedStyle = useAnimatedStyle(() => {
    const translateX = interpolate(progress.value, [0, 1], [-100, 0], Extrapolation.CLAMP)
    return {
      transform: [{ translateX }],
    }
  }, [])

  return (
    <Animated.View style={[{ flex: 1 }, animatedStyle]}>
      <>
        <DrawerHeader />
        <AccountSummary
          onPress={() => navigate(Routes.TeamsNavigator, { screen: Routes.SwitchTeamScreen })}
        />
        <DrawerContentScrollView {...props} contentContainerStyle={{ paddingTop: 0 }}>
          <DrawerItem
            label="Lock"
            icon={(props) => <Icon {...props} source="lock-open" />}
            onPress={console.log}
          />
          <DrawerItem
            label="Translate"
            icon={(props) => <Icon {...props} source="translate" />}
            onPress={console.log}
          />
          <DarkMode closeDrawer={closeDrawer} />
          <Divider />
          <DrawerItem
            label="Home"
            icon={(props) => <Icon {...props} source="home" />}
            onPress={() => navigate(Routes.HomeNavigator)}
          />
          <DrawerItem
            label="Media Library"
            icon={(props) => <Icon {...props} source="folder-multiple-image" />}
            onPress={() => navigate(Routes.MediaNavigator)}
          />
          <DrawerItem
            label="Chats"
            icon={(props) => <Icon {...props} source="chat" />}
            onPress={() => navigate(Routes.ChatsNavigator)}
          />
          <DrawerItem
            label="Addressbook"
            icon={(props) => <Icon {...props} source="card-account-details" />}
            onPress={() => navigate(Routes.AddressbookNavigator)}
          />
          <DrawerItem
            label="Todos"
            icon={(props) => <Icon {...props} source="clipboard-text" />}
            onPress={() => navigate(Routes.TodosNavigator)}
          />
          <DrawerItem
            label="Orders"
            icon={(props) => <Icon {...props} source="cart" />}
            onPress={() => navigate(Routes.OrdersNavigator)}
          />
          <DrawerItem
            label="Deliveries"
            icon={(props) => <Icon {...props} source="truck" />}
            onPress={() => navigate(Routes.DeliveriesNavigator)}
          />
          <Divider />
          <DrawerItem
            label="Account"
            icon={(props) => <Icon {...props} source="account-cog" />}
            onPress={() =>
              navigate(Routes.AccountNavigator, { screen: Routes.AccountSettingsScreen })
            }
          />

          <Logout />
        </DrawerContentScrollView>
        <Text
          variant="bodyMedium"
          style={{ marginLeft: "auto", marginRight: "auto", paddingVertical: 12 }}
        >
          Sowidu v1.0.0
        </Text>
      </>
    </Animated.View>
  )
}

export default DrawerContent
