import { FunctionComponent, useState } from "react"
import { Appbar, Avatar, List, Modal, Surface, useTheme } from "react-native-paper"
import PeopleAutocomplete, { Person } from "../PeopleAutocomplete/PeopleAutocomplete"
import { View } from "react-native"

const fakePeople: Person[] = [
  {
    id: 1,
    urn: "urn:user:1",
    name: "Kin Basco",
    avatar: "https://i.pravatar.cc/300",
  },
  {
    id: 2,
    urn: "urn:user:2",
    name: "Keanne Basco",
    avatar: "https://i.pravatar.cc/300",
  },
  {
    id: 3,
    urn: "urn:user:3",
    name: "Keona Basco",
    avatar: "https://i.pravatar.cc/300",
  },
  {
    id: 4,
    urn: "urn:user:4",
    name: "Dana Luna",
    avatar: "https://i.pravatar.cc/300",
  },
  {
    id: 5,
    urn: "urn:user:5",
    name: "Sebastian Goebel",
    avatar: "https://i.pravatar.cc/300",
  },
]

interface CreateConversationProps {
  visible: boolean
  onDismiss: () => void
}

const CreateConversation: FunctionComponent<CreateConversationProps> = ({ visible, onDismiss }) => {
  const { colors } = useTheme()
  const [searchText, setSearchText] = useState("")
  const [people, setPeople] = useState([...fakePeople])

  const searchResult = fakePeople.filter((i) => !people.find((u) => u.id === i.id))

  const onPersonRemove = (person: Person) => {
    setPeople((prev) => prev.filter((i) => i.id !== person.id))
  }

  const onPersonSelect = (person: Person) => {
    setPeople((prev) => [...prev, person])
    setSearchText("")
  }

  return (
    <Modal visible={visible} onDismiss={onDismiss}>
      <Surface style={{ height: "100%", backgroundColor: colors.background }}>
        <Appbar.Header>
          <Appbar.BackAction onPress={onDismiss} />
          <Appbar.Content title="Create New Conversation" />
        </Appbar.Header>
        <PeopleAutocomplete
          value={people}
          searchText={searchText}
          onSearchTextChange={setSearchText}
          onRemove={onPersonRemove}
        />
        <List.Subheader>People</List.Subheader>
        <View style={{ flex: 1 }}>
          {searchResult.map((i) => (
            <List.Item
              title={i.name}
              descriptionStyle={{ color: colors.outline }}
              left={(props) => <Avatar.Image source={{ uri: i.avatar }} size={44} {...props} />}
              onPress={() => onPersonSelect(i)}
            />
          ))}
        </View>
      </Surface>
    </Modal>
  )
}

export default CreateConversation
