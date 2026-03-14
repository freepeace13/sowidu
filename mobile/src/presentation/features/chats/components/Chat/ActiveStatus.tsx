import { FunctionComponent } from "react"
import { View } from "react-native"
import { Icon, Text } from "react-native-paper"

interface ActiveStatusProps {
  //
}

const ActiveStatus: FunctionComponent<ActiveStatusProps> = () => {
  return (
    <View
      style={{
        flexDirection: "row",
        alignItems: "center",
        gap: 4,
      }}
    >
      <Icon source="circle-slice-8" size={12} color="green" />
      <Text>Active</Text>
    </View>
  )
}

export default ActiveStatus
