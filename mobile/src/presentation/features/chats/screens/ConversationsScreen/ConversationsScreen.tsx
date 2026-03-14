import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { FunctionComponent, useState } from "react"
import { View } from "react-native"

import { Appbar, Avatar, Portal, useTheme } from "react-native-paper"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { DrawerNavigationProp } from "@react-navigation/drawer"

import { Routes } from "@presentation/routes/routes"

import Style from "./ConversationsScreenStyle"
import Conversations from "../../components/Conversations/Conversations"
import { Conversation } from "@domain/chats/Conversation"
import CreateConversation from "../../components/Conversations/CreateConversation"
import { useAccount } from "@presentation/features/account/hooks/useAccount"

interface ConversationsScreenProps {
  //
}

const ConversationsScreen: FunctionComponent<ConversationsScreenProps> = (props) => {
  const { colors } = useTheme()
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()
  const { avatar } = useProfile()
  const { user } = useAccount()
  const [isCreatingConversation, setCreatingConversation] = useState(false)

  const showCreateConversationForm = () => {
    setCreatingConversation(true)
  }

  const hideCreateConversationForm = () => {
    setCreatingConversation(false)
  }

  const handleConversationPress = (conversation: Conversation) => {
    navigation.navigate(Routes.ChatsChatScreen, { conversationId: conversation.id })
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Chats"
        background={colors.inverseOnSurface}
        right={
          <Appbar.Action icon="shape-square-rounded-plus" onPress={showCreateConversationForm} />
        }
        left={
          <Appbar.Action
            isLeading
            size={32}
            icon={(props) => <Avatar.Image {...props} source={{ uri: avatar }} />}
            onPress={navigation.openDrawer}
          />
        }
      />
      <View style={Style.container}>
        {user && <Conversations user={user} onPress={handleConversationPress} />}
        <Portal>
          <CreateConversation
            visible={isCreatingConversation}
            onDismiss={hideCreateConversationForm}
          />
        </Portal>
      </View>
    </ScreenContainer>
  )
}

export default ConversationsScreen
