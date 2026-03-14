import { useKeyboardVisibility } from "core-module"
import React from "react"
import { ScrollView, View, KeyboardAvoidingView } from "react-native"
import { Text, TextInput, Button, Checkbox, Appbar } from "react-native-paper"
import { Stack, Container } from "ui-module"

import Style from "./style"
import { useRegisterForm } from "../../hooks"
import BrandHeading from "../BrandHeading"

export default function RegisterWithEmailScreen({ navigation }) {
  const isKeyboardVisible = useKeyboardVisibility()
  const registerForm = useRegisterForm()
  return (
    <Container>
      <KeyboardAvoidingView style={Style.inner}>
        <Appbar.Header mode="large" style={Style.header}>
          {navigation.canGoBack() && <Appbar.BackAction onPress={navigation.goBack} />}
          <Appbar.Content title={<BrandHeading />} />
        </Appbar.Header>
        <View style={[Style.wrapper, isKeyboardVisible && { flex: 1 }]}>
          <ScrollView keyboardDismissMode="interactive">
            <View style={Style.content}>
              <Stack direction="column">
                <Text variant="bodyMedium" style={{ alignSelf: "center" }}>
                  Create an account
                </Text>
                <TextInput
                  {...registerForm.inputProps.firstName}
                  label="First Name"
                  mode="outlined"
                  activeOutlineColor="#006686"
                  outlineColor="#71787D"
                  textColor="#40484C"
                />
                <TextInput
                  {...registerForm.inputProps.lastName}
                  label="Last Name"
                  mode="outlined"
                  activeOutlineColor="#006686"
                  outlineColor="#71787D"
                  textColor="#40484C"
                />
                <TextInput
                  {...registerForm.inputProps.email}
                  label="Email"
                  mode="outlined"
                  activeOutlineColor="#006686"
                  outlineColor="#71787D"
                  textColor="#40484C"
                />
                <TextInput
                  {...registerForm.inputProps.password}
                  label="Password"
                  mode="outlined"
                  activeOutlineColor="#006686"
                  outlineColor="#71787D"
                  textColor="#40484C"
                />
                <TextInput
                  {...registerForm.inputProps.confirmPassword}
                  label="Confirm Password"
                  mode="outlined"
                  activeOutlineColor="#006686"
                  outlineColor="#71787D"
                  textColor="#40484C"
                />
                <Stack direction="row" style={{ justifyContent: "center" }}>
                  <Checkbox status="unchecked" />
                  <Text>I hereby agree to the Terms and Conditions</Text>
                </Stack>
                <Button
                  onPress={registerForm.submit}
                  disabled={registerForm.isLoading}
                  mode="contained"
                  buttonColor="#006686"
                >
                  Create Account
                </Button>
              </Stack>
            </View>
          </ScrollView>
        </View>
      </KeyboardAvoidingView>
    </Container>
  )
}
