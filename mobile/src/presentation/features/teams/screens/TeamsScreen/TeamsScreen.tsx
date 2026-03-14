import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { View } from "react-native"
import { Appbar, useTheme } from "react-native-paper"

import Style from "./TeamsScreenStyle"
import { useNavigation } from "@react-navigation/native"
import TeamsList, { Entity } from "../../components/TeamsList/TeamsList"
import { useMemo } from "react"
import { useGetTeamsQuery } from "../../teamsApi"
import { Team } from "@domain/teams/team/Team"

function TeamsScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation()
  const { data: teams = [], isLoading } = useGetTeamsQuery(undefined)

  const normalizedTeams = useMemo(() => normalizeTeams(teams), [teams])

  return (
    <ScreenContainer>
      <ScreenHeader
        mode="small"
        title="Organizations"
        background={colors.background}
        canGoBack={navigation.canGoBack()}
        onGoBack={navigation.goBack}
        right={<Appbar.Action icon="plus" />}
      />
      <View style={Style.content}>
        <TeamsList data={normalizedTeams} isLoading={isLoading} onTeamPress={console.log} />
      </View>
    </ScreenContainer>
  )
}

const normalizeTeams = (teams: Team[]) =>
  teams.map(({ id, urn, name, photoURL }) => ({
    id,
    name,
    urn,
    type: "organizational" as Entity["type"],
    photoUri: photoURL,
  }))

export default TeamsScreen
