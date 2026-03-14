import { View } from "react-native"
import { Appbar, TextInput, Button } from "react-native-paper"
import { Container } from "ui-module"

import Style from "./style"

export default function CreateTeamScreen({ navigation }) {
  return (
    <Container>
      <Appbar.Header mode="center-aligned" style={{ backgroundColor: "transparent" }}>
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title="New Organization" />
      </Appbar.Header>
      <View style={Style.content}>
        <View style={Style.formContainer}>
          <TextInput
            label="Name"
            mode="outlined"
            activeOutlineColor="#006686"
            outlineColor="#71787D"
            textColor="#40484C"
          />
          <TextInput
            label="Institution"
            mode="outlined"
            activeOutlineColor="#006686"
            outlineColor="#71787D"
            textColor="#40484C"
          />
          <TextInput
            label="Legal Form"
            mode="outlined"
            activeOutlineColor="#006686"
            outlineColor="#71787D"
            textColor="#40484C"
          />
        </View>
        <Button mode="contained">Create</Button>
      </View>
    </Container>
  )
}
