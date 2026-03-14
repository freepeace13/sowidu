import type { Conversation as ConversationDomain } from "@domain/chats/Conversation"
import { User } from "@domain/user/User"
import { FunctionComponent, useCallback } from "react"
import { StyleProp, StyleSheet, TextStyle, View } from "react-native"
import { Avatar, Icon, List, ListItemProps, Text, useTheme } from "react-native-paper"
import { diffForHumans } from "@presentation/utils/dates"

interface ConversationProps extends Pick<ListItemProps, "onPress" | "onLongPress"> {
  model: ConversationDomain
  user: User
  unread?: boolean
}

const Conversation: FunctionComponent<ConversationProps> = ({
  model,
  user,
  unread = true,
  ...props
}) => {
  const { colors } = useTheme()

  const unreadStyle: StyleProp<TextStyle> = {
    fontFamily: "Roboto_700Bold",
    fontWeight: "700",
  }

  const descriptionStyle: StyleProp<TextStyle> = {
    color: colors.outline,
    ...(unread && unreadStyle),
  }

  const renderMetadata = useCallback<Required<ListItemProps>["right"]>(
    (props) => {
      return (
        <View {...props} style={Style.rightcontainer}>
          <Text style={{ color: colors.outline }} variant="bodySmall">
            {resolveConversationReadableTimestamp(model)}
          </Text>
          {unread && <Icon source="circle" size={10} color={colors.primary} />}
        </View>
      )
    },
    [unread, model, colors]
  )

  const renderAvatar = useCallback<Required<ListItemProps>["right"]>(
    (props) => {
      return (
        <Avatar.Image
          {...props}
          size={44}
          source={{ uri: resolveConversationAvatar(model, user) }}
        />
      )
    },
    [model, user]
  )

  return (
    <List.Item
      {...props}
      left={renderAvatar}
      right={renderMetadata}
      title={resolveConversationName(model, user)}
      titleStyle={{ ...(unread && unreadStyle) }}
      description={resolveConversationLastMessage(model)}
      descriptionStyle={descriptionStyle}
    />
  )
}

const resolveConversationName = (conversation: ConversationDomain, currentUser: User) => {
  return (
    conversation.name ||
    conversation.participants?.find((i) => i.user.urn !== currentUser?.urn)?.user?.name ||
    "Untitled"
  )
}

const resolveConversationAvatar = (conversation: ConversationDomain, currentUser: User) => {
  return (
    conversation.photo ||
    conversation.participants?.find((i) => i.user.urn !== currentUser?.urn)?.user?.photo ||
    "https://doodleipsum.com/700x700/avatar-4?bg=3D27F6&i=96b9ca7a40af431f2f2701e79d242900"
  )
}

const resolveConversationLastMessage = (conversation: ConversationDomain) => {
  return conversation.lastMessage?.body || "You may now start chatting."
}

const resolveConversationReadableTimestamp = (conversation: ConversationDomain) => {
  const lastTimestamp =
    conversation.lastMessage?.createdAt || conversation.updatedAt || new Date().toLocaleString()
  return diffForHumans(lastTimestamp)
}

const Style = StyleSheet.create({
  rightcontainer: {
    flexDirection: "column",
    justifyContent: "space-between",
    alignItems: "flex-end",
  },
})

export default Conversation
