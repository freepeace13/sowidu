import { useColorMode } from "@presentation/features/preference/hooks/useAppearance"
import { Appbar, Portal, useTheme } from "react-native-paper"
import { useSafeAreaInsets } from "react-native-safe-area-context"

type ScreenHeaderPropsType = {
  title: string | React.JSX.Element
  left?: React.JSX.Element
  right?: React.JSX.Element
  dark?: boolean
  canGoBack?: boolean
  background?: string
  mode?: "small" | "medium" | "large" | "center-aligned"
  statusBarHeight?: number
  onGoBack?: () => void
}

function ScreenHeader(props: ScreenHeaderPropsType) {
  const {
    dark,
    statusBarHeight = 0,
    background,
    title,
    mode = "center-aligned",
    onGoBack,
    canGoBack,
    left: leftIcon,
    right: rightIcon,
  } = props
  const { colors } = useTheme()
  const { isDark } = useColorMode()
  const backgroundColor = background ? background : colors.primary
  const isDarkMode = typeof dark !== "undefined" ? dark : isDark
  return (
    <Appbar.Header
      mode={mode}
      elevated
      dark={isDarkMode}
      statusBarHeight={statusBarHeight}
      style={{ backgroundColor }}
    >
      {canGoBack && <Appbar.BackAction onPress={onGoBack} color={colors.onBackground} />}
      {leftIcon && leftIcon}
      <Appbar.Content title={title} color={colors.onBackground} />
      {rightIcon && rightIcon}
    </Appbar.Header>
  )
}

export default ScreenHeader
