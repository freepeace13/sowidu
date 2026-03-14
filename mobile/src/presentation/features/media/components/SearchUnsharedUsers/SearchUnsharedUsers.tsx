import { FunctionComponent, useState } from "react"
import Style from "./SearchUnsharedUsersStyle"
import { View } from "react-native"
import { Avatar, List, Searchbar, Surface, Text } from "react-native-paper"
import { useSearchShareableUsersQuery } from "../../mediaApi"

interface SearchUnsharedUsersProps {
  mediaId: string
  limit?: number
  onPress?: (user: any) => void
}

const SearchUnsharedUsers: FunctionComponent<SearchUnsharedUsersProps> = ({
  mediaId,
  onPress,
  limit = 3,
}) => {
  const [keyword, setKeyword] = useState("")

  const { data = [], isLoading } = useSearchShareableUsersQuery({
    mediaId,
    keyword,
    limit,
  })

  const resetKeyword = () => setKeyword("")

  const handleItemPress = (item: any) => {
    resetKeyword()
    onPress && onPress(item)
  }

  return (
    <View>
      <Searchbar
        placeholder="Add People"
        mode="view"
        loading={isLoading}
        onClearIconPress={resetKeyword}
        onChangeText={setKeyword}
        value={keyword}
      />
      {data.length > 0 ? (
        <Surface style={Style.listSurface}>
          {data.map((user) => (
            <List.Item
              key={user.urn}
              title={<Text variant="titleMedium">{user.name}</Text>}
              description={user.email}
              onPress={() => handleItemPress(user)}
              left={(leftProps) => (
                <List.Icon
                  {...leftProps}
                  icon={(iconProps) => (
                    <Avatar.Image {...iconProps} source={{ uri: user.avatar }} size={36} />
                  )}
                />
              )}
            />
          ))}
        </Surface>
      ) : null}
    </View>
  )
}

export default SearchUnsharedUsers
