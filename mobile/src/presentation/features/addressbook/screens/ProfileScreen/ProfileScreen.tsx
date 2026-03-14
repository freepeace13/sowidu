import { FunctionComponent } from "react"
import { View } from "react-native"

import Style from "./ProfileScreenStyle"
import { Appbar, Avatar, Card, Divider, Icon, List, Text, useTheme } from "react-native-paper"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import { StackNavigationProp } from "@react-navigation/stack"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { ActiveStatus } from "@presentation/components"
import { SectionGroup } from "@presentation/components/SectionGroup"

interface ProfileScreenProps {
  //
}

const ProfileScreen: FunctionComponent<ProfileScreenProps> = () => {
  const { colors } = useTheme()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()
  return (
    <ScreenContainer>
      <Appbar.Header mode="small">
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title={""} />
        <Appbar.Action icon="dots-vertical" />
      </Appbar.Header>
      <View style={Style.container}>
        <View
          style={{
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
            gap: 12,
          }}
        >
          <Avatar.Icon icon="account" size={90} />
          <View
            style={{
              flexDirection: "column",
              alignItems: "center",
              gap: 8,
            }}
          >
            <View
              style={{
                alignItems: "center",
              }}
            >
              <Text variant="titleLarge">John Doe</Text>
              <Text variant="bodyLarge">Anonymous</Text>
              <Text variant="bodySmall" style={{ marginTop: 12 }}>
                Since January, 2022
              </Text>
            </View>
          </View>
        </View>
        <Divider style={{ marginVertical: 12 }} />
        <List.Item
          title="Address"
          description="ajasdj aspoidj asiopdj aspoid jasiopd uj"
          left={(props) => <List.Icon {...props} icon="map-marker" />}
        />
        <List.Item
          title="Phone"
          description="091232103912"
          left={(props) => <List.Icon {...props} icon="phone" />}
        />
        <List.Item
          title="Email"
          description="asdasdsa@gmail.com"
          left={(props) => <List.Icon {...props} icon="email" />}
        />
        <Divider style={{ marginVertical: 12 }} />
      </View>
    </ScreenContainer>
  )
}

export default ProfileScreen
