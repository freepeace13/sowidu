import Stack from "@presentation/components/Stack/Stack"
import { Text, TouchableRipple, useTheme } from "react-native-paper"

import Style from "./RegisterLinkStyle"

type RegisterLinkPropsType = {
  title: string
  label: string
  onPress: () => void
}

function RegisterLink(props: RegisterLinkPropsType) {
  const { colors } = useTheme()
  return (
    <Stack direction="row" space={4} style={Style.stack}>
      <Text>{props.title}</Text>
      <TouchableRipple onPress={props.onPress}>
        <Text style={{ color: colors.primary }}>{props.label}</Text>
      </TouchableRipple>
    </Stack>
  )
}

export default RegisterLink
