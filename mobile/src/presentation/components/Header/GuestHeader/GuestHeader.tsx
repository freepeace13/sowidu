import { View, Image } from "react-native"
import { Text, Appbar, useTheme } from "react-native-paper"

import Style from "./GuestHeaderStyle"

type GuestHeaderPropsType = {
  dark?: boolean
  onGoBack: () => void
  canGoBack: boolean
}

const brandColoredImage = require("../../../../../assets/images/brand-colored.png")
const brandWhiteImage = require("../../../../../assets/images/brand-white.png")

function GuestHeader(props: GuestHeaderPropsType) {
  const { colors } = useTheme()
  const isDark = !!props.dark
  const imageSource = isDark ? brandWhiteImage : brandColoredImage
  const textStyle = { color: isDark ? colors.surfaceVariant : colors.onSurface }
  return (
    <Appbar.Header mode="large" style={Style.header}>
      {props.canGoBack && <Appbar.BackAction onPress={props.onGoBack} />}
      <Appbar.Content
        title={
          <View style={Style.contentWrapper}>
            <View style={Style.brandingImageContainer}>
              <Image source={imageSource} style={Style.brandingImage} resizeMode="center" />
            </View>
            <View style={Style.brandingTextContainer}>
              <Text style={textStyle}>We manage your projects</Text>
            </View>
          </View>
        }
      />
    </Appbar.Header>
  )
}

export default GuestHeader
