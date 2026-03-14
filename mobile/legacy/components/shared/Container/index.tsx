import { PropsWithChildren } from "react"
import { View } from "react-native"
import { useTheme } from "react-native-paper"

import Style from "./style"

export interface ContainerProps extends PropsWithChildren {
  background?: string | undefined
}

export default function Container({ background, children }: ContainerProps) {
  const { colors } = useTheme()
  const containerStyle = {
    backgroundColor: background ? background : colors.background,
  }
  return <View style={[Style.container, containerStyle]}>{children}</View>
}
