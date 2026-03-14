import { useTheme, TouchableRipple, Text } from "react-native-paper"

import Style from "./ForgotPasswordLinkStyle"

type ForgotPasswordLinkPropsType = {
  label: string
  onPress: () => void
}

function ForgotPasswordLink(props: ForgotPasswordLinkPropsType) {
  const { colors } = useTheme()
  return (
    <TouchableRipple onPress={props.onPress} style={Style.container}>
      <Text style={{ color: colors.primary }}>{props.label}</Text>
    </TouchableRipple>
  )
}

export default ForgotPasswordLink
