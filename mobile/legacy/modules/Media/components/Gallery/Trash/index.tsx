import { View } from "react-native"
import { Text, Appbar } from "react-native-paper"
import { Container } from "ui-module"

import Style from "./style"

export default function TrashScreen({ navigation }) {
  return (
    <Container>
      <Appbar.Header statusBarHeight={0} mode="center-aligned">
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title="Trash" />
      </Appbar.Header>
      <View style={Style.content}>
        <Text>Trash</Text>
      </View>
    </Container>
  )
}
