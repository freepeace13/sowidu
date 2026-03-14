import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { ParamListBase, RouteProp, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import { FunctionComponent } from "react"
import { View } from "react-native"
import { Appbar, Button, useTheme } from "react-native-paper"

import Style from "./ManageTeamMemberScreenStyle"
import MemberProfile from "../../components/MemberProfile/MemberProfile"

type RouteParams = { params: { memberId: number; teamId: number } }

const ManageTeamMemberScreen: FunctionComponent<any> = () => {
  const { colors } = useTheme()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()
  const route = useRoute<RouteProp<RouteParams>>()

  return (
    <ScreenContainer>
      <Appbar.Header mode="small">
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title={""} />
        <Button icon="trash-can" onPress={console.log} textColor={colors.error}>
          Remove
        </Button>
      </Appbar.Header>
      <View style={Style.content}>
        <MemberProfile teamId={route.params.teamId} memberId={route.params.memberId} />
      </View>
    </ScreenContainer>
  )
}

export default ManageTeamMemberScreen
