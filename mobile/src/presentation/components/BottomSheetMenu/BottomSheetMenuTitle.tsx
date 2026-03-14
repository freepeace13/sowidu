import { View } from "react-native"
import { Icon, Text, useTheme } from "react-native-paper"

import Style from "./BottomSheetMenuStyle"

type Props = {
  icon?: string
  title: string
}

function BottomSheetMenuTitle(props: Props) {
  const { icon, title } = props
  const { colors } = useTheme()
  return (
    <View style={Style.title}>
      {icon && (
        <>
          <Icon source={icon} size={24} color={colors.primary} />
          <View style={Style.spacer} />
        </>
      )}
      <Text variant="titleMedium">{title}</Text>
    </View>
  )
}

export default BottomSheetMenuTitle
