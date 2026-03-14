import React from "react"
import { View, ViewStyle } from "react-native"
import { ActivityIndicator, Text } from "react-native-paper"

import Style from "./style"

interface Props {
  color?: any
  text?: string
  containerStyle?: ViewStyle
}

export default function Loading(props: Props) {
  const { color = "white", text, containerStyle } = props
  return (
    <View style={[Style.container, containerStyle]}>
      <ActivityIndicator color={color} />
      {text && <Text style={{ color }}>{text}</Text>}
    </View>
  )
}
