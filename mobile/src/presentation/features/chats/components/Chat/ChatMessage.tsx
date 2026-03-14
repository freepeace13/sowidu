import { FunctionComponent } from "react"
import { View } from "react-native"

import Style from "./ChatMessageStyle"
import { List, Text, useTheme } from "react-native-paper"

export interface ChatMessageProps {
  text: string
  sender?: boolean
  status?: string
}

const ChatMessage: FunctionComponent<ChatMessageProps> = ({ text, sender = true, status }) => {
  const { colors } = useTheme()
  return (
    <View style={[Style.container, sender ? Style.sender : Style.receiver]}>
      <List.Item
        title={text}
        style={{
          paddingVertical: 0,
          paddingRight: 0,
          maxWidth: "60%",
        }}
        titleStyle={{
          ...(sender
            ? { color: colors.onPrimary, borderBottomRightRadius: 0 }
            : { borderBottomLeftRadius: 0 }),
          backgroundColor: sender ? colors.primary : colors.outlineVariant,
          borderRadius: 12,
          paddingHorizontal: 14,
          paddingVertical: 8,
          marginRight: 0,
          marginLeft: 0,
        }}
        titleNumberOfLines={0}
        contentStyle={{
          paddingLeft: 0,
          // borderWidth: 1,
          // borderColor: "green",
          // backgroundColor: sender ? colors.primary : colors.outlineVariant,
          // borderRadius: 12,
        }}
        description={status ? status : undefined}
        descriptionStyle={{
          alignSelf: sender ? "flex-end" : "flex-start",
          marginHorizontal: 12,
          paddingVertical: 4,
        }}
        // left={(props) => <List.Icon icon="pencil" {...props} />}
      />
    </View>
  )
}

export default ChatMessage
