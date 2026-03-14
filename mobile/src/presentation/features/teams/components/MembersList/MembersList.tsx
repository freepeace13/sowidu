import { Member } from "@domain/teams/members/Member"
import { FunctionComponent, useCallback } from "react"
import { FlatList, ListRenderItemInfo, StyleProp, ViewStyle } from "react-native"
import { Avatar, Card, Divider, useTheme } from "react-native-paper"
import { useGetMembersQuery } from "../../teamsApi"
import { ThemeProp } from "react-native-paper/lib/typescript/types"
import { useAccount } from "@presentation/features/account/hooks/useAccount"

interface MembersListProps {
  teamId: number
  onPress: (member: Member) => void
}

const MembersList: FunctionComponent<MembersListProps> = ({ teamId, onPress }) => {
  const { user } = useAccount()
  const { data: items = [], isLoading, refetch } = useGetMembersQuery({ teamId })

  const keyExtractor = useCallback((member: Member) => member.memberId.toString(), [])

  const renderItem = useCallback(
    ({ item }: ListRenderItemInfo<Member>) => (
      <MemberCard
        key={item.memberId}
        name={item.userInfo.name}
        suffix={user?.id === item.userInfo.id ? " (You)" : ""}
        role={item.role}
        avatar={item.userInfo.photoURL}
        onPress={() => onPress(item)}
      />
    ),
    [onPress, user]
  )

  return (
    <FlatList
      data={items}
      onRefresh={refetch}
      ItemSeparatorComponent={() => <Divider />}
      keyExtractor={keyExtractor}
      refreshing={isLoading}
      renderItem={renderItem}
    />
  )
}

interface MemberCardProps {
  name: string
  suffix?: string
  avatar: string
  role: string
  onPress?: () => void
}

const MemberCard: FunctionComponent<MemberCardProps> = ({ onPress, avatar, suffix, ...props }) => {
  const { colors } = useTheme()
  const cardTheme: ThemeProp = { roundness: 0, colors: { outline: colors.outlineVariant } }
  const cardStyle: StyleProp<ViewStyle> = { backgroundColor: colors.background }
  return (
    <Card mode="contained" style={cardStyle} theme={cardTheme} onPress={onPress}>
      <Card.Title
        title={props.name + (suffix || "")}
        titleVariant="titleMedium"
        subtitle={props.role}
        subtitleVariant="bodyMedium"
        subtitleStyle={{ color: colors.outline }}
        left={(leftProps) => <Avatar.Image {...leftProps} source={{ uri: avatar }} />}
      />
    </Card>
  )
}

export default MembersList
