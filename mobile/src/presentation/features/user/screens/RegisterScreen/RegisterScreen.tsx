import GuestHeader from "@presentation/components/Header/GuestHeader/GuestHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import RegisterForm from "@presentation/features/user/components/RegisterForm/RegisterForm"
import { useNavigation } from "@react-navigation/native"
import { View } from "react-native"

import Style from "./RegisterScreenStyle"
import { useRegisterMutation } from "../../userApi"
import { StatusBar } from "expo-status-bar"
import { useTheme } from "react-native-paper"
import { KeyboardAwareScrollView } from "react-native-keyboard-controller"

function RegisterScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation()
  const [register, { isLoading }] = useRegisterMutation()

  const handleRegister = async (formData: any) => {
    await register({
      name: `${formData.firstName} ${formData.lastName}`,
      email: formData.email,
      password: formData.password,
    }).unwrap()
  }

  return (
    <ScreenContainer>
      <StatusBar translucent={false} backgroundColor={colors.background} />
      <GuestHeader canGoBack={navigation.canGoBack()} onGoBack={navigation.goBack} />
      <View style={Style.content}>
        <KeyboardAwareScrollView
          style={Style.keyboardAwareScrollView}
          contentContainerStyle={Style.keyboardAwareContent}
        >
          <RegisterForm
            title="Create an account"
            isLoading={isLoading}
            onRegister={handleRegister}
          />
        </KeyboardAwareScrollView>
      </View>
    </ScreenContainer>
  )
}

export default RegisterScreen
