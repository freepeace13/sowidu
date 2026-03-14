import { MediaUser } from "@domain/media/shares/MediaUser"
import { Nullable } from "@domain/shared/types"
import { MediaReadWritePermission } from "@domain/media/types"
import { Animated, View } from "react-native"
import { Swipeable, RectButton } from "react-native-gesture-handler"
import {
  ActivityIndicator,
  Avatar,
  Button,
  Icon,
  List,
  Surface,
  Text,
  useTheme,
} from "react-native-paper"
import { useGetSharedUsersQuery } from "../../mediaApi"

import Style from "./SharedPeopleStyle"

interface Props {
  mediaId: string
  onRemove: (user: MediaUser) => void
}

const readableScope = (permission: Nullable<MediaReadWritePermission>) => {
  if (permission === MediaReadWritePermission.Read) {
    return "Read"
  } else if (permission === MediaReadWritePermission.ReadWrite) {
    return "Read/Write"
  }
}

function SharedPeople({ mediaId, onRemove }: Props) {
  const { colors } = useTheme()
  const { data = [], isLoading } = useGetSharedUsersQuery({ mediaId })

  const renderRight = (user: MediaUser) => {
    return (
      <Button
        disabled={user.isOwner}
        contentStyle={{ flexDirection: "row-reverse" }}
        onPress={() => console.log("change role", user)}
      >
        {user.isOwner ? "Owner" : readableScope(user.scopes)}
      </Button>
    )
  }

  const renderRightActions = (user: MediaUser) =>
    function DeleteActionButton(
      _progress: Animated.AnimatedInterpolation<number>,
      _dragX: Animated.AnimatedInterpolation<number>
    ) {
      return (
        <RectButton
          onPress={() => onRemove(user)}
          style={{
            ...Style.removeButton,
            backgroundColor: colors.errorContainer,
          }}
        >
          <Icon source="trash-can" color={colors.error} size={24} />
        </RectButton>
      )
    }

  if (isLoading) {
    return (
      <View style={Style.loading}>
        <ActivityIndicator />
      </View>
    )
  }

  return (
    <List.Section>
      <List.Subheader>Shared People</List.Subheader>
      {data.map((user) => (
        <Swipeable
          key={`shared_${user.urn}`}
          friction={2}
          rightThreshold={40}
          renderRightActions={!user.isOwner ? renderRightActions(user) : undefined}
        >
          <Surface style={{ backgroundColor: "#f8f9fb" }}>
            <List.Item
              title={<Text variant="titleMedium">{user.name}</Text>}
              description={user.email}
              right={() => renderRight(user)}
              left={(leftProps) => (
                <List.Icon
                  {...leftProps}
                  icon={(iconProps) => (
                    <Avatar.Image {...iconProps} source={{ uri: user.avatar }} size={36} />
                  )}
                />
              )}
            />
          </Surface>
        </Swipeable>
      ))}
    </List.Section>
  )
}

export default SharedPeople
