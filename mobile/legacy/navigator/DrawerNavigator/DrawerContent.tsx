import { DrawerContentScrollView } from "@react-navigation/drawer"
import { Api as AuthApi, useUserInfo } from "auth-module"
import { useAppDispatch } from "core-module"
import { Image } from "expo-image"
import { Constants as MediaConstants } from "media-module"
import { View } from "react-native"
import { Appbar, Divider, Avatar, List, useTheme, Text } from "react-native-paper"

import Style from "./style"

export default function DrawerContent({ navigation }) {
  const { colors } = useTheme()
  const userInfo = useUserInfo()
  const dispatch = useAppDispatch()

  const renderListIcon = (icon) => {
    return (props) => <List.Icon {...props} color={colors.secondary} icon={icon} />
  }

  return (
    <View style={[Style.drawerContainer, { backgroundColor: colors.surface }]}>
      <DrawerHeader />
      <List.Section style={Style.wrapper}>
        <List.Item
          title={userInfo.currentTeam?.name ? userInfo.currentTeam.name : userInfo.name}
          description="Software Engineer"
          contentStyle={Style.listItem}
          left={(props) => (
            <List.Icon
              icon={() => (
                <Avatar.Image
                  {...props}
                  size={40}
                  source={{
                    uri: userInfo.currentTeam?.id ? userInfo.currentTeam.photo : userInfo.photo,
                  }}
                />
              )}
            />
          )}
        />
      </List.Section>
      <DrawerContentScrollView
        contentContainerStyle={Style.scrollViewContent}
        style={Style.wrapper}
      >
        <List.Section style={Style.listSection}>
          <List.Item
            title="Lock"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("lock-open")}
          />
          <List.Item
            title="Media Library"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("folder-multiple-image")}
            onPress={() => navigation.navigate(MediaConstants.RouteNames.MediaNavigator)}
          />
          <List.Item
            title="Orders"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("cart")}
          />
          <List.Item
            title="Addressbook"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("clipboard-account")}
          />
          <List.Item title="Chats" titleStyle={Style.listItemTitle} left={renderListIcon("chat")} />
          <List.Item
            title="Todos"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("clipboard-text")}
          />
          <List.Item
            title="Delivery Tickets"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("ticket-account")}
          />
        </List.Section>
        <Divider style={Style.divider} />
        <List.Section style={Style.listSection}>
          <List.Item
            title="Account Settings"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("account-cog")}
          />
          <List.Item
            title="Translate"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("translate")}
          />
          <List.Item
            title="Switch Account"
            titleStyle={Style.listItemTitle}
            style={Style.listItem}
            theme={{ roundness: 100 }}
            left={renderListIcon("swap-horizontal")}
            onPress={() => navigation.navigate("SwitchTeamNavigator")}
          />
          <List.Item
            title="Dark Mode"
            titleStyle={Style.listItemTitle}
            contentStyle={{}}
            left={renderListIcon("theme-light-dark")}
            onPress={() => navigation.navigate("DarkModeScreen")}
          />
          <List.Item
            title="Log out"
            titleStyle={Style.listItemTitle}
            left={renderListIcon("logout")}
            onPress={() => dispatch(AuthApi.logout.initiate())}
          />
        </List.Section>
      </DrawerContentScrollView>
      <Text variant="bodySmall" style={[Style.version, { color: colors.outline }]}>
        Sowidu v1.0.0
      </Text>
    </View>
  )
}

function DrawerHeader() {
  return (
    <Appbar.Header mode="center-aligned">
      <Appbar.Content
        title={
          <Avatar.Image
            size={70}
            style={Style.brandAvatar}
            source={(props) => (
              <Image
                {...props}
                source={require("../../../assets/images/adaptive-icon-dark.png")}
                contentFit="contain"
                contentPosition="center"
                style={Style.brandImage}
              />
            )}
          />
        }
      />
    </Appbar.Header>
  )
}
