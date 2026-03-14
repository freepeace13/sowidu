import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { Routes } from "@presentation/routes/routes"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { useCallback, useMemo } from "react"
import { View } from "react-native"
import { Button, Divider, useTheme } from "react-native-paper"

import Style from "./SwitchTeamScreenStyle"
import TeamsList, { Entity } from "../../components/TeamsList/TeamsList"
import { useGetTeamsQuery } from "../../teamsApi"
import { useSwitchAccountMutation } from "@presentation/features/account/accountApi"
import { StackNavigationProp } from "@react-navigation/stack"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { User } from "@domain/user/User"
import { CurrentTeam, Team } from "@domain/teams/team/Team"

function SwitchTeamScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()
  const { data: teams = [], isLoading } = useGetTeamsQuery(undefined)
  const { user, currentTeam } = useAccount()
  const [switchAccount] = useSwitchAccountMutation()

  const mergedAccounts = useMemo(
    () => [normalizeUser(user as User), ...teams.map(normalizeTeam)],
    [user, teams]
  )

  const handleAddOrganizationPress = () => {
    navigation.navigate(Routes.CreateTeamScreen)
  }

  const handleSwitchAccount = useCallback(
    async ({ urn }: Entity) => {
      try {
        await switchAccount({ urn }).unwrap()
        navigation.goBack()
      } catch (error) {
        console.log(error)
      }
    },
    [navigation, switchAccount]
  )

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Switch Account"
        background={colors.background}
        onGoBack={navigation.goBack}
        canGoBack={navigation.canGoBack()}
      />
      <Divider />
      <View style={Style.content}>
        <TeamsList
          data={mergedAccounts}
          isLoading={isLoading}
          onTeamPress={handleSwitchAccount}
          isActive={(account) =>
            isSelected({
              account,
              currentTeam,
              user,
            })
          }
        />
        <View style={Style.footer}>
          <Button
            mode="contained-tonal"
            buttonColor={colors.primary}
            textColor={colors.surface}
            onPress={handleAddOrganizationPress}
            theme={{ colors: { outline: colors.surfaceDisabled } }}
          >
            Add Organization
          </Button>
        </View>
      </View>
    </ScreenContainer>
  )
}

const isSelected = ({
  account,
  currentTeam,
  user,
}: {
  account: Entity
  currentTeam?: CurrentTeam | null
  user?: User
}) => {
  if (!user) return false
  return account.type === "personal"
    ? !currentTeam?.id && user.id === account.id
    : account.id === currentTeam?.id
}

const baseAccount = (item: Team | User) => ({
  id: item.id,
  urn: item.urn,
  name: "fullName" in item ? item.fullName : item.name,
  photoUri: item?.photoURL as string,
})

const normalizeTeam = (team: Team): Entity => ({
  ...baseAccount(team),
  type: "organizational",
})

const normalizeUser = (user: User): Entity => ({
  ...baseAccount(user),
  type: "personal",
})

export default SwitchTeamScreen
