import Constants from "expo-constants"
import { Constants as MediaConstants } from "media-module"
import { cloneElement, isValidElement } from "react"
import { View, Image } from "react-native"
import { List, Divider, Text, Appbar, useTheme, Card, Avatar } from "react-native-paper"
import { Container, Illustrations } from "ui-module"

import Style from "./style"
import { Screen, Loading } from "../shared"

type HomeLink = {
  title: string
  description: string
  icon: string
  iconBackgroundColor: string
  onPress?: () => void
}

export default function HomeScreen({ navigation }) {
  const { colors } = useTheme()
  const HomeLinks: (HomeLink | React.JSX.Element)[] = [
    {
      title: "Media Library",
      description: "100 Files",
      icon: "folder-multiple-image",
      iconBackgroundColor: "#D32F2F",
      onPress: () => navigation.navigate("MediaNavigator"),
    },
    <Divider style={Style.divider} />,
    {
      title: "Todo",
      description: "100 Files",
      icon: "calendar-text",
      iconBackgroundColor: "#F2C24F",
      onPress: () => navigation.openDrawer(),
    },
    <Divider style={Style.divider} />,
    {
      title: "Addressbook",
      description: "100 Files",
      icon: "account-box",
      iconBackgroundColor: "#3949AB",
    },
  ]
  const renderLink = (link: HomeLink | React.JSX.Element, key) => {
    if (isValidElement(link)) {
      return cloneElement(link, { ...(link as React.JSX.Element).props, key })
    }
    const { iconBackgroundColor, icon, description, title } = link as HomeLink
    return (
      <List.Item
        {...link}
        key={key}
        style={Style.listItem}
        title={() => (
          <Text style={{ color: "#191C1E" }} variant="titleMedium">
            {title}
          </Text>
        )}
        description={() => (
          <Text style={{ color: "#8F9193" }} variant="bodyMedium">
            {description}
          </Text>
        )}
        left={() => (
          <View
            style={[
              Style.listIconContainer,
              {
                backgroundColor: iconBackgroundColor,
              },
            ]}
          >
            <List.Icon icon={icon} color="#FFF" />
          </View>
        )}
      />
    )
  }

  const renderCard = ({ title, subtitle, icon, iconColor, ...rest }) => (
    <Card
      {...rest}
      mode="outlined"
      theme={{ roundness: 3, colors: { outline: colors.outlineVariant } }}
    >
      <Card.Title
        title={title}
        subtitle={subtitle}
        titleVariant="titleMedium"
        subtitleVariant="bodyMedium"
        left={(props) => (
          <Avatar.Icon
            {...props}
            size={40}
            icon={icon}
            color={colors.surface}
            style={{ backgroundColor: iconColor }}
          />
        )}
      />
    </Card>
  )

  return (
    <Container>
      <Appbar.Header mode="center-aligned" dark style={{ backgroundColor: colors.primary }}>
        <Appbar.Action icon="menu" onPress={navigation.openDrawer} />
        <Appbar.Content
          title={
            <Image
              source={Illustrations.Images.brandWhite}
              style={Style.headerImage}
              resizeMode="contain"
            />
          }
        />
      </Appbar.Header>
      <View style={Style.content}>
        {renderCard({
          title: "Media Library",
          subtitle: "300 file(s)",
          icon: "folder-multiple-image",
          iconColor: "#d32f2f",
          onPress: () => navigation.navigate(MediaConstants.RouteNames.MediaNavigator),
        })}
        {renderCard({
          title: "Orders",
          subtitle: "300 contact(s)",
          icon: "cart",
          iconColor: "#2196f3",
        })}
        {renderCard({
          title: "Addressbook",
          subtitle: "300 contact(s)",
          icon: "clipboard-account",
          iconColor: "#3949ab",
        })}
        {renderCard({
          title: "Chats",
          subtitle: "2 new message(s)",
          icon: "chat",
          iconColor: "#71269c",
        })}
        {renderCard({
          title: "Todo",
          subtitle: "2 board(s)",
          icon: "clipboard-text",
          iconColor: "#f2c24f",
        })}
        {renderCard({
          title: "Delivery Tickets",
          subtitle: "30 ticket(s)",
          icon: "ticket-account",
          iconColor: "#27ae60",
        })}
      </View>
    </Container>
  )
}
