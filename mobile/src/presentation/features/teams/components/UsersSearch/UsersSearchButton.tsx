import { useUsersSearch } from "./UsersSearchProvider"
import { TextInput, useTheme } from "react-native-paper"

const UsersSearchButton: React.FC = (props) => {
  const { colors } = useTheme()
  const { onPrompt } = useUsersSearch()
  return (
    <TextInput.Icon {...props} icon="account-search" color={colors.secondary} onPress={onPrompt} />
  )
}

export default UsersSearchButton
