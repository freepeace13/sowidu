import { useCallback } from "react"
import { GestureResponderEvent } from "react-native"
import { List, useTheme } from "react-native-paper"

type BottomSheetMenuItemPropsType = {
  title: string
  icon?: string
  onPress?: (e: GestureResponderEvent) => void
  disabled?: boolean
}

function BottomSheetMenuItem(props: BottomSheetMenuItemPropsType) {
  const { colors } = useTheme()
  const theme = {
    colors: {
      onSurface: props.disabled ? colors.onSurfaceDisabled : colors.onSurface,
      onSurfaceVariant: props.disabled ? colors.onSurfaceDisabled : colors.onSurfaceVariant,
    },
  }
  return (
    <List.Item
      title={props.title}
      theme={theme}
      disabled={props.disabled}
      left={(iconProps) => (props.icon ? <List.Icon {...iconProps} icon={props.icon} /> : null)}
      onPress={props.onPress}
    />
  )
}

export default BottomSheetMenuItem
