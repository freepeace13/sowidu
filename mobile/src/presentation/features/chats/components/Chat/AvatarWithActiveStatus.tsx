import { FunctionComponent } from "react"
import { View } from "react-native"
import { Avatar, Icon } from "react-native-paper"

interface AvatarWithActiveStatusProps {}

const AvatarWithActiveStatus: FunctionComponent<AvatarWithActiveStatusProps> = () => {
  return (
    <View style={{ position: "relative" }}>
      <Avatar.Icon icon="account" size={38} />
      <View
        style={{
          borderRadius: 10,
          position: "absolute",
          bottom: 0,
          right: 0,
          backgroundColor: "white",
        }}
      >
        <Icon source="circle-slice-8" size={12} color="green" />
      </View>
    </View>
  )
}

export default AvatarWithActiveStatus
