import { Team } from "@domain/teams/team/Team"
import { Card } from "react-native-paper"

type TeamCardPropsType = {
  team: Team
}

function TeamCard(props: TeamCardPropsType) {
  return (
    <Card>
      <Card.Content>
        <Card.Title title={props.team.name} />
      </Card.Content>
    </Card>
  )
}

export default TeamCard
