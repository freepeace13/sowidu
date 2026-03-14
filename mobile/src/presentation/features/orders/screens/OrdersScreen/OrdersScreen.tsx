import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { DrawerNavigationProp } from "@react-navigation/drawer"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { FunctionComponent } from "react"
import { View } from "react-native"
import { Appbar, Avatar, List, useTheme } from "react-native-paper"

import Style from "./OrdersScreenStyle"

interface OrdersScreenProps {
  //
}

const OrdersScreen: FunctionComponent<OrdersScreenProps> = () => {
  const { colors } = useTheme()
  const profile = useProfile()
  const navigation = useNavigation<DrawerNavigationProp<ParamListBase>>()
  return (
    <ScreenContainer>
      <ScreenHeader
        title="Orders"
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
        <List.Subheader>TOdos</List.Subheader>
        {/*  */}
      </View>
    </ScreenContainer>
  )
}

export default OrdersScreen
