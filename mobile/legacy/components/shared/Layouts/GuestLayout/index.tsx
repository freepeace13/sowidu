import { useNavigation } from "@react-navigation/native"
import { View, Image } from "react-native"
import { Appbar, Text } from "react-native-paper"

import Style from "./style"
import Container from "../../Container"
import * as Illustrations from "../../Illustrations"

function Heading() {
  return (
    <View style={Style.brandWrapper}>
      <View style={Style.brandContainer}>
        <Image
          source={Illustrations.Images.brandColored}
          style={Style.brandImage}
          resizeMode="contain"
        />
      </View>
      <View style={{ alignItems: "center" }}>
        <Text>We manage your projects</Text>
      </View>
    </View>
  )
}

export default function GuestLayout({ contentStyle = null, children }) {
  const navigation = useNavigation()
  return (
    <Container>
      <Appbar.Header mode="large" elevated style={Style.header}>
        {navigation.canGoBack() && <Appbar.BackAction onPress={navigation.goBack} />}
        <Appbar.Content title={<Heading />} />
      </Appbar.Header>
      <View style={[Style.content, contentStyle]}>{children}</View>
    </Container>
  )
}
