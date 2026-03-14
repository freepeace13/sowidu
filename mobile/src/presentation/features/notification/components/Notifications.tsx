import { FlatList } from "react-native"
import { Card, Text, Avatar, useTheme } from "react-native-paper"

import Style from "./NotificationsStyle"

type NotificationsPropsType = any

const cardTheme = () => ({
  roundness: 0,
  colors: { surfaceVariant: "#f8f9fb" },
})

const cardStyle = ({ colors }) => [Style.card, { borderColor: colors.surfaceDisabled }]

function Notifications(props: NotificationsPropsType) {
  const { colors } = useTheme()
  return (
    <FlatList
      data={fakeNotifications}
      renderItem={({ item }) => (
        <Card mode="contained" theme={cardTheme()} style={cardStyle({ colors })}>
          <Card.Title
            title={item.title}
            titleNumberOfLines={1}
            titleVariant="titleMedium"
            subtitle={item.description}
            subtitleNumberOfLines={2}
            right={() => <Text variant="labelMedium">23m ago</Text>}
            rightStyle={Style.timestamp}
            left={() => <Avatar.Icon size={44} icon="file" />}
          />
        </Card>
      )}
    />
  )
}

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

export default Notifications
