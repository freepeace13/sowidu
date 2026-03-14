import React, { useMemo } from "react"
import { View } from "react-native"
import { Text, useTheme } from "react-native-paper"

import Style from "./style"
import BrandTextSvg from "../BrandTextSvg"

export default function Brand({
  dark = false,
  height = 220,
  subtitle,
}: {
  height?: number
  dark?: boolean
  subtitle: string
}) {
  const { colors } = useTheme()

  const subtitleColor = useMemo(
    () => (dark ? colors.onPrimaryContainer : colors.inverseOnSurface),
    [dark],
  )

  return (
    <View style={[Style.container, { height }]}>
      <BrandTextSvg dark={dark} />
      {/* <Text style={{ color: titleColor }} variant="displayLarge">
        Sowidu
      </Text> */}
      <Text style={{ color: subtitleColor }} variant="bodyMedium">
        {subtitle}
      </Text>
    </View>
  )
}
