import { FunctionComponent, useRef } from "react"
import { View } from "react-native"
import { Avatar, Chip, TextInput, useTheme } from "react-native-paper"

import Style from "./PeopleAutocompleteStyle"

export type Person = {
  id: number
  urn: string
  name: string
  avatar: string
}

interface PeopleAutocompleteProps {
  value: Person[]
  searchText: string | undefined
  onSearchTextChange: (value: string) => void
  onRemove: (value: Person) => void
}

const PeopleAutocomplete: FunctionComponent<PeopleAutocompleteProps> = ({
  value,
  searchText,
  onSearchTextChange,
  onRemove,
}) => {
  const { colors } = useTheme()
  const inputRef = useRef(null)
  return (
    <View
      style={[
        Style.container,
        { borderBottomColor: colors.outlineVariant, backgroundColor: colors.inverseOnSurface },
      ]}
    >
      {value.map((person) => (
        <View key={person.id}>
          <Chip
            avatar={<Avatar.Image source={{ uri: person.avatar }} size={24} />}
            onClose={() => onRemove(person)}
          >
            {person.name}
          </Chip>
        </View>
      ))}

      <TextInput
        ref={inputRef}
        underlineColor="transparent"
        activeUnderlineColor="transparent"
        mode="flat"
        cursorColor={colors.outlineVariant}
        value={searchText}
        onChangeText={onSearchTextChange}
        style={Style.textInput}
      />
    </View>
  )
}

export default PeopleAutocomplete
