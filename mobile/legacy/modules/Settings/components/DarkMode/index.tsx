import { View } from "react-native"
import { RadioButton, Appbar, useTheme } from "react-native-paper"
import { Container } from "ui-module"

import Style from "./style"
import { useColorScheme } from "../../hooks"

export default function DarkModeScreen({ navigation }) {
  const [colorScheme, setColorScheme] = useColorScheme()
  return (
    <Container>
      <Appbar.Header mode="center-aligned">
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title="Dark Mode" />
      </Appbar.Header>
      <View style={Style.content}>
        <RadioButton.Group onValueChange={setColorScheme} value={colorScheme}>
          <RadioButton.Item label="On" value="dark" />
          <RadioButton.Item label="Off" value="light" />
          <RadioButton.Item label="Use System Settings" value="auto" />
        </RadioButton.Group>
      </View>
    </Container>
  )
}
