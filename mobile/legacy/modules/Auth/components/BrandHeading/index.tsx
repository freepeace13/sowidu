import { useMemo } from "react"
import { View, Image, ImageSourcePropType } from "react-native"
import { Text, useTheme } from "react-native-paper"
import { Illustrations } from "ui-module"

import Style from "./style"

interface BandHeadingProps {
  dark?: boolean
}

export default function BrandHeading({ dark = false }: BandHeadingProps) {
  const { colors } = useTheme()
  const image = dark ? Illustrations.Images.brandWhite : Illustrations.Images.brandColored
  const textStyle = { color: dark ? colors.surfaceVariant : colors.onSurface }
  return (
    <View>
      <View style={Style.imageContainer}>
        <Image source={image} style={Style.brandImage} resizeMode="contain" />
      </View>
      <View style={{ alignItems: "center" }}>
        <Text style={textStyle}>We manage your projects</Text>
      </View>
    </View>
  )
}
