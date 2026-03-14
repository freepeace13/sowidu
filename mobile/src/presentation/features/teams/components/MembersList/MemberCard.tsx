import { StyleProp, ViewStyle } from "react-native"
import { Card, useTheme, Avatar } from "react-native-paper"
import { ThemeProp } from "react-native-paper/lib/typescript/types"
import { FunctionComponent } from "react"

interface TeamMemberCardProps {
  name: string
  avatar: string
  role: string
  onPress?: () => void
}

const TeamMemberCard: FunctionComponent<TeamMemberCardProps> = ({ onPress, avatar, ...props }) => {
  const { colors } = useTheme()
  const cardTheme: ThemeProp = { roundness: 0, colors: { outline: colors.outlineVariant } }
  const cardStyle: StyleProp<ViewStyle> = { backgroundColor: colors.background }
  return (
    <Card mode="contained" style={cardStyle} theme={cardTheme} onPress={onPress}>
      <Card.Title
        title={props.name}
        titleVariant="titleMedium"
        subtitle={props.role}
        subtitleVariant="bodyMedium"
        subtitleStyle={{ color: colors.outline }}
        left={(leftProps) => <Avatar.Image {...leftProps} source={{ uri: avatar }} />}
      />
    </Card>
  )
}

export default TeamMemberCard
