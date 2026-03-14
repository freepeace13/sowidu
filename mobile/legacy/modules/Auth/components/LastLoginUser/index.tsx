import { View, Keyboard } from "react-native"
import { Text, Button, TouchableRipple, useTheme, Appbar, Avatar } from "react-native-paper"
import { PasswordInput, Container, Stack } from "ui-module"

import Style from "./style"
import AuthConstants from "../../constants"
import { useLoginForm, useUserInfo } from "../../hooks"
import BrandHeading from "../BrandHeading"

const RegisterRoute = AuthConstants.RouteNames.RegisterWithEmail

export default function LastLoginUserScreen({ navigation }) {
  const { colors } = useTheme()
  const userInfo = useUserInfo()
  const loginForm = useLoginForm({ email: userInfo.email })
  return (
    <Container>
      <Appbar.Header mode="large" style={Style.header}>
        {navigation.canGoBack() && <Appbar.BackAction onPress={navigation.goBack} />}
        <Appbar.Content title={<BrandHeading />} />
      </Appbar.Header>
      <View style={Style.content}>
        <Stack direction="column" space={28}>
          <View style={{ rowGap: 12 }}>
            <Avatar.Image
              source={{ uri: userInfo.photo }}
              size={90}
              style={{ marginLeft: "auto", marginRight: "auto" }}
            />
            <Text variant="titleMedium" style={{ alignSelf: "center" }}>
              {userInfo.name}
            </Text>
          </View>
          <View style={{ rowGap: 12 }}>
            <PasswordInput
              {...loginForm.inputProps.password}
              label="Password"
              onSubmitEditing={Keyboard.dismiss}
            />
            <Button
              onPress={loginForm.submit}
              mode="contained"
              buttonColor={colors.primary}
              disabled={loginForm.isLoading}
            >
              Log in
            </Button>
          </View>
          <Stack direction="row" space={4} style={{ justifyContent: "center" }}>
            <Text>Do you have any account?</Text>
            <TouchableRipple onPress={() => navigation.navigate(RegisterRoute)}>
              <Text style={{ color: colors.primary }}>Create account</Text>
            </TouchableRipple>
          </Stack>
        </Stack>
      </View>
    </Container>
  )
}
