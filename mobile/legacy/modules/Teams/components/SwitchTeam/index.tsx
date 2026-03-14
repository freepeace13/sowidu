import { useUserInfo } from "auth-module"
import { useMemo } from "react"
import { View, Image, FlatList, ListRenderItem } from "react-native"
import { Appbar, Avatar, Button, Card, Divider, Text, useTheme } from "react-native-paper"
import { Illustrations, Container } from "ui-module"

import Style from "./style"
import { Api as TeamsApi } from "../../services"
import type { TeamInfo } from "../../types"

function useTeamList() {
  const userInfo = useUserInfo()
  const { data = [], isFetching, refetch } = TeamsApi.useGetTeamsQuery()

  const teamsData = useMemo(() => {
    const currentTeamId = userInfo.currentTeam?.id

    if (currentTeamId) {
      return data.filter((i) => i.id !== currentTeamId)
    }

    return data
  }, [userInfo, data])

  return {
    data: teamsData,
    isFetching,
    refetch,
  }
}

type TeamProps = {
  info: TeamInfo
  onPress: (info: TeamInfo) => void
  subtitle?: string | undefined
}

function ListCard({ info, onPress, subtitle }: TeamProps) {
  const { colors } = useTheme()
  return (
    <Card
      mode="outlined"
      key={info.id}
      style={Style.teamCard}
      theme={{
        roundness: 3,
        colors: {
          background: colors.background,
          outline: colors.outlineVariant,
        },
      }}
      onPress={() => onPress(info)}
    >
      <Card.Title
        title={info.name}
        titleNumberOfLines={1}
        titleVariant="titleMedium"
        subtitle={subtitle}
        left={() => <Avatar.Image source={{ uri: info.photo }} size={44} />}
      />
    </Card>
  )
}

type UserAccountProps = {
  onPress: () => void
}

function UserAccount(props: UserAccountProps) {
  const userInfo = useUserInfo()

  if (!userInfo.currentTeam?.id) {
    return null
  }

  const info = {
    id: userInfo.id,
    name: userInfo.name,
    photo: userInfo.photo,
  }

  return (
    <>
      <ListCard {...props} subtitle="Personal Account" info={info} />
      <Divider style={{ marginBottom: 12 }} />
    </>
  )
}

function TeamsList() {
  const [switchTeam] = TeamsApi.useSwitchTeamMutation()
  const { data, isFetching, refetch } = useTeamList()

  const renderItem: ListRenderItem<TeamInfo> = ({ item }) => {
    return (
      <ListCard
        info={item}
        subtitle="Organization"
        onPress={(info) => switchTeam({ teamId: info.id })}
      />
    )
  }

  return (
    <FlatList
      data={data}
      onRefresh={refetch}
      refreshing={isFetching}
      style={Style.teamList}
      renderItem={renderItem}
      ListHeaderComponent={<UserAccount onPress={() => switchTeam({ teamId: null })} />}
    />
  )
}

export default function SwitchTeamScreen({ navigation }) {
  return (
    <Container>
      <Appbar.Header style={{ backgroundColor: "transparent" }}>
        <Appbar.BackAction onPress={navigation.goBack} />
      </Appbar.Header>
      <View style={Style.content}>
        <ContentHeading />
        <TeamsList />
        <Button mode="contained" onPress={() => navigation.navigate("CreateTeamScreen")}>
          Add another account
        </Button>
        {/* <Button>SkipLangSa</Button> */}
      </View>
    </Container>
  )
}

function ContentHeading() {
  return (
    <View style={Style.brandWrapper}>
      <View style={Style.brandContainer}>
        <Image
          source={Illustrations.Images.brandColored}
          style={Style.brandImage}
          resizeMode="contain"
        />
      </View>
      <View style={{ alignItems: "center" }}>
        <Text>Login into your team</Text>
      </View>
    </View>
  )
}
