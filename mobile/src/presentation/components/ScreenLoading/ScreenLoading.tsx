import Stack from "@presentation/components/Stack/Stack"
import { PropsWithChildren, useEffect } from "react"
import { View, Image } from "react-native"
import { useTheme, Text, ActivityIndicator } from "react-native-paper"

import Style from "./ScreenLoadingStyle"

type ScreenLoadingPropsType = PropsWithChildren & {
  isLoading: boolean
  loadingText?: string
}

const containerStyle = (colors) => [Style.container, { backgroundColor: colors.primary }]

function ScreenLoading(props: ScreenLoadingPropsType) {
  const { colors } = useTheme()
  return !props.isLoading ? (
    props.children
  ) : (
    <View style={containerStyle(colors)}>
      <View style={Style.imgContainer}>
        <Image
          source={require("@assets/images/brand-icon-white.png")}
          style={Style.imgLogo}
          resizeMode="contain"
        />
      </View>
      <Stack direction="row" space={8} style={Style.spinnerContainer}>
        <ActivityIndicator color={colors.surfaceVariant} size={15} />
        {props.loadingText && (
          <Text variant="labelLarge" style={{ color: colors.surfaceVariant }}>
            {props.loadingText}
          </Text>
        )}
      </Stack>
    </View>
  )
}

export default ScreenLoading
