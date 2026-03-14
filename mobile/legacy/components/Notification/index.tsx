import { View, FlatList } from "react-native"
import { Text, Appbar, Card, Avatar, useTheme } from "react-native-paper"
import { Container } from "ui-module"

import Style from "./style"

const fakeNotifications = [
  {
    id: 1,
    title: "Lorem ipsum dolor, sit amet consectetur adipisicing elit",
    description:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quaerat magni, praesentium earum fuga dignissimos exercitationem distinctio architecto. Incidunt corrupti architecto, nobis in quos consequuntur, laborum, id autem non molestiae animi.",
  },
  {
    id: 2,
    title: "Lorem ipsum dolor, sit amet consectetur adipisicing elit",
    description:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quaerat magni, praesentium earum fuga dignissimos exercitationem distinctio architecto. Incidunt corrupti architecto, nobis in quos consequuntur, laborum, id autem non molestiae animi.",
  },
  {
    id: 3,
    title: "Lorem ipsum dolor, sit amet consectetur adipisicing elit",
    description:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quaerat magni, praesentium earum fuga dignissimos exercitationem distinctio architecto. Incidunt corrupti architecto, nobis in quos consequuntur, laborum, id autem non molestiae animi.",
  },
  {
    id: 4,
    title: "Lorem ipsum dolor, sit amet consectetur adipisicing elit",
    description:
      "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quaerat magni, praesentium earum fuga dignissimos exercitationem distinctio architecto. Incidunt corrupti architecto, nobis in quos consequuntur, laborum, id autem non molestiae animi.",
  },
]

export default function NotificationScreen({ navigation }) {
  const { colors } = useTheme()
  return (
    <Container>
      <Appbar.Header mode="center-aligned" dark style={{ backgroundColor: colors.primary }}>
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title="Notifications" />
      </Appbar.Header>
      <View style={Style.content}>
        <FlatList
          data={fakeNotifications}
          renderItem={({ item }) => (
            <Card
              mode="contained"
              theme={{
                roundness: 0,
                colors: { surfaceVariant: "#f8f9fb" },
              }}
              style={{
                borderBottomWidth: 1,
                paddingVertical: 8,
                borderColor: colors.surfaceDisabled,
              }}
            >
              <Card.Title
                title={item.title}
                titleNumberOfLines={1}
                titleVariant="titleMedium"
                subtitle={item.description}
                subtitleNumberOfLines={2}
                right={() => <Text variant="labelMedium">23m ago</Text>}
                rightStyle={{ alignSelf: "flex-start", marginTop: 5, marginRight: 16 }}
                left={() => <Avatar.Icon size={44} icon="file" />}
              />
            </Card>
          )}
        />
      </View>
    </Container>
  )
}
