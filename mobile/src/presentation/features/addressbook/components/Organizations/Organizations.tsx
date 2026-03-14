import { FunctionComponent } from "react"
import { FlatList, View } from "react-native"
import { Avatar, List } from "react-native-paper"

type OrganizationType = {
  id: number
  name: string
  email: string
}

const fakeOrganizations: OrganizationType[] = [
  {
    id: 1,
    name: "Kin Basco",
    email: "freepeace13@gmail.com",
  },
  {
    id: 2,
    name: "Keanne Gabriel L. Basco",
    email: "keanne.basco@gmail.com",
  },
]

interface OrganizationsProps {
  onPress: (organization: any) => void
}

const Organizations: FunctionComponent<OrganizationsProps> = ({ onPress }) => {
  return (
    <View>
      <List.Subheader>Organizations (3)</List.Subheader>
      <FlatList
        data={fakeOrganizations}
        keyExtractor={(item) => item.id.toString()}
        renderItem={({ item }) => (
          <List.Item
            title={item.name}
            description={item.email}
            left={(props) => (
              <List.Icon
                {...props}
                icon={(props) => <Avatar.Icon {...props} icon="account" size={36} />}
              />
            )}
            onPress={() => onPress(item)}
            right={(props) => <List.Icon {...props} icon="dots-vertical" />}
          />
        )}
      />
    </View>
  )
}

export default Organizations
