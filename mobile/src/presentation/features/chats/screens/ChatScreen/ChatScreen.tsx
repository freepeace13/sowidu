import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { FunctionComponent } from "react"
import { View } from "react-native"
import Conversation from "../../components/Chat/Conversation"

import Style from "./ChatScreenStyle"
import { ParamListBase, RouteProp, useNavigation, useRoute } from "@react-navigation/native"
import { Appbar, Avatar, Icon, List, Text, useTheme } from "react-native-paper"
import { DrawerNavigationProp } from "@react-navigation/drawer"
import { ChatMessageProps } from "../../components/Chat/ChatMessage"
import AvatarWithActiveStatus from "../../components/Chat/AvatarWithActiveStatus"

const history: ChatMessageProps[] = [
  { text: "Hmmm🤔" },
  { text: "It looks like it still will be choppy..." },
  { text: "But I don't know what should I try next" },
  { text: "Reanimated?", sender: false },
  { text: "A little bit disappointed 😔" },
  { text: "🤯" },
  { text: "Try to check it. I hope it helps you...", sender: false },
  { text: "It really pushes you to think twice on how to design it first" },
  {
    text: "Looks promising!😎 I was always looking for a solution that would allow us to run animations on native thread and provide at least stable 60 FPS",
  },
  { text: "You have to check it!!!", sender: false },
  { text: "Ha-ha! I'm definitely going to check it!" },
  { text: "Hello! How are you?" },
  { text: "Hi! I'm good. How are you?", sender: false },
  {
    text: "I'm fine, thank you! Have you seen new keyboard animation library?",
  },
  { text: "No! Let me check.", sender: false },
  {
    text: "Wow! I've been looking for it for a while. It's awesome!",
    sender: false,
  },
  {
    text: "a while. It's awesome!",
    sender: true,
    status: "Sent",
  },
]

const reversedMessages = [...history].reverse()

interface ChatScreenProps {}

const ChatScreen: FunctionComponent<ChatScreenProps> = () => {
  const { colors, fonts } = useTheme()
  const route = useRoute<RouteProp<{ params: { conversationId: number } }>>()
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()
  console.log(route.params?.conversationId)
  return (
    <ScreenContainer>
      <Appbar.Header
        mode="small"
        elevated
        statusBarHeight={0}
        style={{ backgroundColor: colors.background }}
      >
        {navigation.canGoBack() && (
          <Appbar.Action icon="chevron-left" onPress={navigation.goBack} />
          // <Appbar.BackAction onPress={navigation.goBack} color={colors.onBackground} />
        )}
        <Appbar.Content
          title={
            <List.Item
              title="Sebastian Goebel"
              titleStyle={fonts.titleMedium}
              description="Active"
              left={(props) => <List.Icon icon={(props) => <AvatarWithActiveStatus />} />}
            />
          }
        />
        <Appbar.Action icon="dots-vertical" />
      </Appbar.Header>
      {/* <ScreenHeader
        title="Sebastian Goebel"
        mode="small"
        background={colors.inverseOnSurface}
        onGoBack={navigation.goBack}
        canGoBack={navigation.canGoBack()}
        left={
          <Appbar.Action
            size={32}
            onPress={console.log}
            icon={(iconProps) => <Avatar.Icon {...iconProps} icon="account" />}
          />
        }
        right={<Appbar.Action icon="phone" />}
      /> */}
      <View style={Style.container}>
        <Conversation messages={reversedMessages} />
      </View>
    </ScreenContainer>
  )
}

export default ChatScreen
