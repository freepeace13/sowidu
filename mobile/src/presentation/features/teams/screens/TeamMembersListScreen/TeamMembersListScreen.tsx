import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { Appbar, Divider, useTheme } from "react-native-paper"

import { Routes } from "@presentation/routes/routes"

import { View } from "react-native"
import { FunctionComponent, useCallback } from "react"
import { Member } from "@domain/teams/members/Member"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"

import Style from "./TeamMembersListScreenStyle"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import MembersList from "../../components/MembersList/MembersList"

/** @add asd */
const TeamMembersListScreen: FunctionComponent<any> = () => {
  const { colors } = useTheme()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()
  const { currentTeam } = useAccount()

  const addMember = useCallback(() => {
    navigation.navigate(Routes.TeamInviteMemberScreen)
  }, [navigation])

  const handlePress = (member: Member) => {
    navigation.navigate(Routes.ManageTeamMemberScreen, {
      memberId: member.memberId,
      teamId: member.teamId,
    })
  }

  if (!currentTeam) {
    return null
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Manage Members"
        background={colors.background}
        onGoBack={navigation.goBack}
        canGoBack={navigation.canGoBack()}
        right={<Appbar.Action icon="account-plus" color={colors.secondary} onPress={addMember} />}
      />
      <Divider />
      <View style={Style.content}>
        <MembersList teamId={currentTeam?.id} onPress={handlePress} />
      </View>
    </ScreenContainer>
  )
}

export default TeamMembersListScreen
