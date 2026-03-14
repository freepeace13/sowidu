import { useKeyboardVisibility } from "core-module"
import React from "react"
import { View, Keyboard } from "react-native"
import { Text, TextInput, Button, TouchableRipple, useTheme, Appbar } from "react-native-paper"
import { PasswordInput, Container, Stack, Illustrations } from "ui-module"

import Style from "./style"
import AuthConstants from "../../constants"
import { useLoginForm, useUserInfo } from "../../hooks"
import BrandHeading from "../BrandHeading"

const RegisterRoute = AuthConstants.RouteNames.RegisterWithEmail

// @todo: Display previously signin user avatar and email with password input

export default function LoginWithEmailScreen({ navigation }) {
  const isKeyboardVisible = useKeyboardVisibility()
  const { colors } = useTheme()
  const userInfo = useUserInfo()
  const loginForm = useLoginForm()
  console.log(userInfo)
  return (
    <Container>
      <Appbar.Header mode="large" style={Style.header}>
        {navigation.canGoBack() && <Appbar.BackAction onPress={navigation.goBack} />}
        <Appbar.Content title={<BrandHeading />} />
      </Appbar.Header>
      <View style={Style.content}>
        {!isKeyboardVisible && <Illustrations.Svg.LoginImage />}
        <Stack direction="column">
          <Text variant="bodyMedium" style={{ alignSelf: "center" }}>
            Please, Log into your account
          </Text>
          <TextInput
            {...loginForm.inputProps.email}
            label="Email"
            mode="outlined"
            onSubmitEditing={Keyboard.dismiss}
            activeOutlineColor={colors.primary}
            outlineColor="#71787D"
            textColor="#40484C"
            autoComplete="email"
            keyboardType="email-address"
            textContentType="emailAddress"
            autoCapitalize="none"
          />
          <PasswordInput
            {...loginForm.inputProps.password}
            label="Password"
            onSubmitEditing={Keyboard.dismiss}
          />
          <TouchableRipple style={{ marginLeft: "auto" }}>
            <Text style={{ color: colors.primary }}>Forgot your password?</Text>
          </TouchableRipple>
          <Button
            onPress={loginForm.submit}
            mode="contained"
            buttonColor={colors.primary}
            disabled={loginForm.isLoading}
          >
            Log in
          </Button>
          <Stack direction="row" space={4} style={{ justifyContent: "center" }}>
            <Text>Do you have any account?</Text>
            <TouchableRipple onPress={() => navigation.navigate(RegisterRoute)}>
              <Text style={{ color: colors.primary }}>Create Account</Text>
            </TouchableRipple>
          </Stack>
        </Stack>
      </View>
    </Container>
  )
}
