import { View } from "react-native"
import { Appbar, useTheme, Button } from "react-native-paper"
import { Container, Illustrations, Stack } from "ui-module"

import Style from "./style"
import AuthConstants from "../../constants"
import BrandHeading from "../BrandHeading"

const LoginRoute = AuthConstants.RouteNames.LoginWithEmail
const RegisterRoute = AuthConstants.RouteNames.RegisterWithEmail

export default function IntroductionScreen({ navigation }) {
  const { colors } = useTheme()
  return (
    <Container background={colors.primary}>
      <Appbar.Header mode="large" style={Style.header}>
        <Appbar.Content title={<BrandHeading dark />} />
      </Appbar.Header>
      <View style={Style.content}>
        <Illustrations.Svg.IntroductionImage />
        <Stack direction="column">
          <Button
            mode="contained-tonal"
            textColor={colors.primary}
            onPress={() => navigation.navigate(LoginRoute)}
          >
            Log in
          </Button>
          <Button
            mode="outlined"
            textColor={colors.surfaceVariant}
            theme={{ colors: { outline: colors.surfaceVariant } }}
            onPress={() => navigation.navigate(RegisterRoute)}
          >
            Create account
          </Button>
        </Stack>
      </View>
    </Container>
  )
}
