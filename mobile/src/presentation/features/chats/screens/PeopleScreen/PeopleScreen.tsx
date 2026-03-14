import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { DrawerNavigationProp } from "@react-navigation/drawer"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { FunctionComponent } from "react"
import { View } from "react-native"
import { Appbar, Avatar, List, useTheme } from "react-native-paper"

import Style from "./PeopleScreenStyle"
import AvatarWithActiveStatus from "../../components/Chat/AvatarWithActiveStatus"

interface PeopleScreenProps {
  //
}

const PeopleScreen: FunctionComponent<PeopleScreenProps> = () => {
  const { colors } = useTheme()
  const profile = useProfile()
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()
  return (
    <ScreenContainer>
      <ScreenHeader
        title="People"
        background={colors.inverseOnSurface}
        right={<Appbar.Action icon="magnify" onPress={navigation.openDrawer} />}
        left={
          <Appbar.Action
            isLeading
            size={32}
            icon={(iconProps) => (
              <Avatar.Image
                {...iconProps}
                source={{
                  uri: profile.avatar,
                }}
              />
            )}
            onPress={navigation.openDrawer}
          />
        }
      />
      <View style={Style.container}>
        <List.Subheader>People</List.Subheader>
        <List.Item
          title="Kin Basco"
          description="freepeace13@gmail.com"
          left={(props) => <List.Icon {...props} icon={(props) => <AvatarWithActiveStatus />} />}
          right={(props) => <List.Icon {...props} icon="dots-vertical" />}
        />
        <List.Item
          title="Keona Glisha Basco"
          description="keona.basco@gmail.com"
          left={(props) => <List.Icon {...props} icon={(props) => <AvatarWithActiveStatus />} />}
          right={(props) => <List.Icon {...props} icon="dots-vertical" />}
        />
        {/*  */}
      </View>
    </ScreenContainer>
  )
}

export default PeopleScreen
