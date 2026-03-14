import { ucFirst } from "@presentation/utils/string"
import { FlatList, ListRenderItem, View } from "react-native"
import { Avatar, Card, Icon, useTheme } from "react-native-paper"

import Style from "./TeamsListStyle"
import { FunctionComponent } from "react"

export interface Entity {
  id: number
  urn: string
  type: "organizational" | "personal"
  name: string
  photoUri: string
}

interface TeamsListProps {
  data: Entity[]
  isLoading?: boolean
  isActive?: (value: Entity) => boolean
  onTeamPress: (value: Entity) => void
}

const TeamsList: FunctionComponent<TeamsListProps> = ({
  onTeamPress,
  data,
  isActive,
  isLoading,
}) => {
  const { colors } = useTheme()
  const renderItem: ListRenderItem<Entity> = ({ item }) => (
    <Card
      mode="outlined"
      key={`${item.type}_${item.id}`}
      style={[Style.card, { backgroundColor: colors.background }]}
      theme={{
        colors: {
          outline: isActive && isActive(item) ? colors.tertiary : colors.surfaceDisabled,
        },
      }}
      onPress={() => onTeamPress(item)}
    >
      <Card.Title
        titleVariant="titleMedium"
        subtitleVariant="bodySmall"
        title={item.name}
        subtitle={`${ucFirst(item.type)} Account`}
        left={(leftProps) => <Avatar.Image {...leftProps} source={{ uri: item.photoUri }} />}
        right={(rightProps) =>
          isActive && isActive(item) ? (
            <View style={{ marginRight: 12 }}>
              <Icon {...rightProps} source="check-decagram" color={colors.primary} />
            </View>
          ) : null
        }
      />
    </Card>
  )
  return (
    <View style={Style.container}>
      <FlatList data={data} refreshing={isLoading} renderItem={renderItem} />
    </View>
  )
}

export default TeamsList
