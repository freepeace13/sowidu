import { View } from "react-native"
import { Text, useTheme } from "react-native-paper"

export default function AccountSettingsScreen() {
  const { colors } = useTheme()
  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
      <Text>AccountSettings</Text>
    </View>
  )
}
