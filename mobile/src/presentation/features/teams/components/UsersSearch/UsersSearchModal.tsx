import { useMemo, useState } from "react"
import {
  Avatar,
  Card,
  Icon,
  IconButton,
  Modal,
  Text,
  TextInput,
  TouchableRipple,
  useTheme,
} from "react-native-paper"

import Style from "./UsersSearchStyle"
import { FlatList, View } from "react-native"
import {
  DataType,
  useInviteTeamMemberSearchAsync,
} from "../../hooks/useInviteTeamMemberSearchAsync"

interface UsersSearchModalProps {
  title: string
  teamId: number
  visible: boolean
  onDismiss: () => void
  onItemPress?: (email: string) => void
}

const UsersSearchModal: React.FC<UsersSearchModalProps> = ({
  teamId,
  title,
  visible,
  onDismiss,
  onItemPress,
}: UsersSearchModalProps) => {
  const { colors } = useTheme()
  const [searchText, setSearchText] = useState("")
  const { data, searchPeople, clearData, isLoading } = useInviteTeamMemberSearchAsync(teamId, {
    limit: 5,
  })

  const opacity = useMemo(() => (isLoading ? 0.5 : 1), [isLoading])

  const handleSearchChange = (text: string) => {
    setSearchText(text)
    searchPeople(text)
  }

  const handleItemPress = (item: DataType | string) => {
    if (onItemPress) {
      onItemPress(typeof item === "string" ? item : item.email)
    }

    clearData()
    setSearchText("")
  }

  return (
    <Modal visible={visible} onDismiss={onDismiss} contentContainerStyle={Style.modal}>
      <View style={[Style.wrapper, { backgroundColor: colors.background }]}>
        <View style={Style.header}>
          <View style={Style.title}>
            <Icon source="account-search" size={42} color={colors.secondary} />
            <Text variant="titleLarge" style={{ color: colors.onBackground }}>
              {title}
            </Text>
          </View>
          <IconButton icon="close" onPress={onDismiss} iconColor={colors.outline} />
        </View>
        <View style={Style.searches}>
          <TextInput
            mode="outlined"
            outlineColor={colors.outlineVariant}
            right={<TextInput.Icon icon="magnify" color={colors.secondary} />}
            placeholder="Search by name or email"
            onChangeText={handleSearchChange}
            style={Style.textInput}
          />
          <View style={{ opacity }}>
            <FlatList
              data={data}
              renderItem={({ item }) => (
                <TouchableRipple onPress={() => handleItemPress(item)}>
                  <Card.Title
                    title={item.name}
                    subtitle={item.email}
                    left={(leftProps) => (
                      <Avatar.Image {...leftProps} source={{ uri: item.photo }} />
                    )}
                  />
                </TouchableRipple>
              )}
              ListEmptyComponent={
                searchText ? (
                  <TouchableRipple onPress={() => handleItemPress(searchText)}>
                    <Card.Title
                      title={searchText}
                      left={(leftProps) => <Avatar.Icon {...leftProps} icon="email" />}
                    />
                  </TouchableRipple>
                ) : (
                  <Text variant="titleSmall" style={Style.emptyText}>
                    No data to show
                  </Text>
                )
              }
            />
          </View>
        </View>
      </View>
    </Modal>
  )
}

export default UsersSearchModal
