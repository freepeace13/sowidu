import { Image } from "react-native"
import { Appbar, Avatar, useTheme } from "react-native-paper"

import Style from "./HomeHeaderStyle"
import { useProfile } from "@presentation/features/account/hooks/useProfile"

type HomeHeaderPropsType = {
  onMenuPress: () => void
}

const brandWhite = require("../../../../../../assets/images/brand-white.png")

function HomeHeader(props: HomeHeaderPropsType) {
  const { colors } = useTheme()
  const profile = useProfile()
  return (
    <Appbar.Header
      mode="center-aligned"
      dark
      style={{ backgroundColor: colors.primary }}
      statusBarHeight={0}
    >
      <Appbar.Action
        size={32}
        onPress={props.onMenuPress}
        icon={(iconProps) => (
          <Avatar.Image
            {...iconProps}
            source={{
              uri: profile.avatar,
            }}
          />
        )}
      />
      <Appbar.Content
        title={<Image source={brandWhite} style={Style.image} resizeMode="contain" />}
      />
    </Appbar.Header>
  )
}

export default HomeHeader
