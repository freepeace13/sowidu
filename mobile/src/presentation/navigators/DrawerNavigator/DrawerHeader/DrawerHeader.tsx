import { Image } from "expo-image"
import { Appbar, Avatar } from "react-native-paper"

import Style from "./DrawerHeaderStyle"

function DrawerHeader() {
  return (
    <Appbar.Header
      mode="center-aligned"
      style={{ backgroundColor: "transparent" }}
      statusBarHeight={0}
    >
      <Appbar.Content
        title={
          <Avatar.Image
            size={70}
            style={Style.avatar}
            source={(props) => (
              <Image
                {...props}
                source={require("../../../../../assets/images/adaptive-icon-dark.png")}
                contentFit="contain"
                contentPosition="center"
                style={Style.image}
              />
            )}
          />
        }
      />
    </Appbar.Header>
  )
}

export default DrawerHeader
