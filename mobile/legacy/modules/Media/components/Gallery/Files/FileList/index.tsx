import { useContext } from "react"
import { FlatList, FlatListProps, ImageSourcePropType } from "react-native"
import { Card, Avatar, CardProps, useTheme } from "react-native-paper"

import Style from "./style"
import * as MediaConstants from "../../../../constants"
import { Api as MediaApi, Utils as MediaUtils } from "../../../../services"
import { ListType, FilesContext } from "../Context"

type ItemType = (typeof MediaConstants.MediaTypes)[keyof typeof MediaConstants.MediaTypes]

export interface Item {
  id: string
  title: string
  type: ItemType
  subtitle: string
  coverPhoto?: ImageSourcePropType
  coverPhotoHidden?: boolean
}

interface ItemProps {
  item: Item
  mode?: CardProps["mode"]
  onPress?: (item: Item) => void
}

export function FileListItem({ mode = "outlined", item, onPress }: ItemProps) {
  const { colors } = useTheme()
  const renderLeftIcon = () => {
    return (
      <Avatar.Icon
        size={36}
        icon={MediaConstants.MediaTypeIcons[item.type]}
        theme={{
          colors: {
            onPrimary: "white",
            primary: MediaConstants.MediaTypeColors[item.type],
          },
        }}
      />
    )
  }
  return (
    <Card
      mode={mode}
      theme={{ roundness: 3, colors: { outline: colors.outlineVariant } }}
      onPress={() => onPress(item)}
      style={Style.listItemCard}
    >
      {!item.coverPhotoHidden && <Card.Cover source={item.coverPhoto} />}
      <Card.Title
        title={item.title}
        subtitle={item.subtitle}
        style={Style.listItem}
        titleVariant="titleMedium"
        subtitleVariant="bodyMedium"
        left={renderLeftIcon}
      />
    </Card>
  )
}

interface ListProps {
  type: ListType
  items: Item[]
  refreshing?: FlatListProps<Item>["refreshing"]
  onRefresh?: FlatListProps<Item>["onRefresh"]
  mode?: ItemProps["mode"]
  onItemPress: ItemProps["onPress"]
}

function FileList({ mode, type, items, refreshing, onRefresh, onItemPress }: ListProps) {
  return (
    <FlatList
      data={items}
      onRefresh={onRefresh}
      refreshing={refreshing}
      keyExtractor={(i) => i.id.toString()}
      renderItem={({ item }) => (
        <FileListItem
          onPress={onItemPress}
          mode={mode}
          item={{
            ...item,
            coverPhotoHidden: type === "list",
          }}
        />
      )}
    />
  )
}

export default function FileListContainer() {
  const { listType, setCurrentItem } = useContext(FilesContext)
  const { data = [], isLoading, refetch } = MediaApi.useGetMediaFilesQuery({})

  const shapedItems: Item[] = data.map((i) => ({
    id: i.id,
    title: i.title,
    type: i.file.type,
    subtitle: MediaUtils.formatBytes(i.file.size),
    coverPhoto: MediaUtils.withAuthorizationHeader({ uri: i.file.thumbnail }),
  }))

  return (
    <FileList
      type={listType}
      onItemPress={setCurrentItem}
      refreshing={isLoading}
      onRefresh={refetch}
      items={shapedItems}
    />
  )
}
