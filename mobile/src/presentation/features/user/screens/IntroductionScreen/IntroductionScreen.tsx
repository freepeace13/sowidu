import GuestHeader from "@presentation/components/Header/GuestHeader/GuestHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { StatusBar } from "expo-status-bar"
import { FunctionComponent } from "react"
import { View } from "react-native"
import { Button, useTheme } from "react-native-paper"
import Style from "./IntroductionScreenStyle"
import IntroductionImage from "@assets/images/introduction.svg"
import { useNavigation } from "@react-navigation/native"
import { Routes } from "@presentation/routes/routes"

interface IntroductionScreenProps {
  //
}

const IntroductionScreen: FunctionComponent<IntroductionScreenProps> = (props) => {
  const { colors } = useTheme()
  const navigation = useNavigation()
  return (
    <ScreenContainer background={colors.primary}>
      <StatusBar translucent backgroundColor={colors.primary} style="light" />
      <GuestHeader dark canGoBack={false} onGoBack={() => {}} />
      <View style={Style.content}>
        <View style={Style.illustration}>
          <IntroductionImage />
        </View>
        <View style={{ gap: 12, padding: 12 }}>
          <Button
            mode="outlined"
            theme={{ colors: { outline: colors.outlineVariant } }}
            textColor={colors.primary}
            buttonColor={colors.background}
            onPress={() => navigation.navigate(Routes.LoginScreen)}
          >
            Log in
          </Button>
          <Button
            mode="outlined"
            theme={{ colors: { outline: colors.outlineVariant } }}
            textColor={colors.background}
            onPress={() => navigation.navigate(Routes.RegisterScreen)}
          >
            Create Account
          </Button>
        </View>
      </View>
    </ScreenContainer>
  )
}

export default IntroductionScreen
