import { FunctionComponent } from "react"
import { Appbar, useTheme } from "react-native-paper"

interface ChatHeaderProps {
  canGoBack: boolean
  onGoBack: () => void
}

const ChatHeader: FunctionComponent<ChatHeaderProps> = ({ canGoBack, onGoBack }) => {
  const { colors } = useTheme()
  return (
    <Appbar.Header
      mode="medium"
      elevated
      dark={false}
      statusBarHeight={0}
      style={{ backgroundColor: colors.inverseOnSurface }}
    >
      {canGoBack && <Appbar.BackAction onPress={onGoBack} color={colors.onBackground} />}
      <Appbar.Content title="conversation" color={colors.onBackground} />
    </Appbar.Header>
  )
}

export default ChatHeader
