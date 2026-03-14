import LoginImage from "@assets/images/login-image.svg"
import GuestHeader from "@presentation/components/Header/GuestHeader/GuestHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import LoginForm from "@presentation/features/user/components/LoginForm/LoginForm"
import { Routes } from "@presentation/routes/routes"
import { useNavigation } from "@react-navigation/native"
import { View } from "react-native"
import { KeyboardAwareScrollView } from "react-native-keyboard-controller"

import Style from "./LoginScreenStyle"
import { useLoginMutation } from "../../userApi"
import { StatusBar } from "expo-status-bar"
import { useTheme } from "react-native-paper"

function LoginScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation()
  const [loginCredentials, { isLoading }] = useLoginMutation()

  const handleLogin = async (formData: any) => {
    await loginCredentials({
      email: formData.email,
      password: formData.password,
    }).unwrap()
  }

  return (
    <ScreenContainer>
      <StatusBar translucent={false} backgroundColor={colors.background} />
      <GuestHeader canGoBack={false} onGoBack={() => navigation.goBack()} />
      <View style={Style.content}>
        <KeyboardAwareScrollView
          style={Style.keyboardAwareScrollView}
          contentContainerStyle={Style.keyboardAwareContent}
        >
          <View style={Style.illustration}>
            <LoginImage />
          </View>
          <LoginForm
            title="Please, Log into your account"
            onLogin={handleLogin}
            isLoading={isLoading}
            onForgotPassword={() => {}}
            onRegister={() => navigation.navigate(Routes.RegisterScreen)}
          />
        </KeyboardAwareScrollView>
      </View>
    </ScreenContainer>
  )
}

export default LoginScreen
