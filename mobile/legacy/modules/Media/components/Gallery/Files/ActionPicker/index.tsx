import { useNavigation } from "@react-navigation/native"
import React, { useContext } from "react"
import { View } from "react-native"
import { Divider, List, Text, Portal } from "react-native-paper"
import { BottomSheet } from "ui-module"

import Style from "./style"
import * as MediaConstants from "../../../../constants"
import { useFileRenameContext } from "../../../FileRename"
import { FilesContext } from "../Context"

export enum Action {
  Open = "OPEN",
  ShowDetails = "SHOW_DETAILS",
  TagAddress = "TAG_ADDRESS",
  TagCategory = "TAG_CATEGORY",
  Rename = "RENAME",
  Download = "DOWNLOAD",
  SendTo = "SEND_TO",
  MoveToTrash = "MOVE_TO_TRASH",
}

const Items: { label: string; value: Action; icon: string }[] = [
  {
    label: "Open",
    value: Action.Open,
    icon: "eye",
  },
  {
    label: "Show Details",
    value: Action.ShowDetails,
    icon: "information",
  },
  {
    label: "Tag to address",
    value: Action.TagAddress,
    icon: "map-marker",
  },
  {
    label: "Tag to category",
    value: Action.TagCategory,
    icon: "shape",
  },
  {
    label: "Rename",
    value: Action.Rename,
    icon: "pencil",
  },
  {
    label: "Download",
    value: Action.Download,
    icon: "download",
  },
  {
    label: "Send to",
    value: Action.SendTo,
    icon: "send",
  },
  {
    label: "Move to trash",
    value: Action.MoveToTrash,
    icon: "delete",
  },
]

interface Props {
  title?: string | React.ReactNode
  visible: boolean
  closeOnBackdropTouch?: boolean
  onPick: (type: Action) => void
  onDismiss: () => void
}

function ActionPicker({ title, visible, onDismiss, onPick, closeOnBackdropTouch = true }: Props) {
  return (
    <BottomSheet
      onClose={onDismiss}
      isOpen={visible}
      enableOverDrag={false}
      enableBackdropTouch={closeOnBackdropTouch}
    >
      <BottomSheet.ScrollView>
        {title && (
          <>
            <View style={Style.titleContainer}>
              <Text variant="titleSmall" numberOfLines={1}>
                {title}
              </Text>
            </View>
            <Divider />
          </>
        )}
        <Divider />
        {Items.map((i) => (
          <List.Item
            key={i.value}
            title={i.label}
            left={(props) => <List.Icon {...props} icon={i.icon} />}
            onPress={() => onPick(i.value)}
          />
        ))}
      </BottomSheet.ScrollView>
    </BottomSheet>
  )
}

const Routes = {
  [MediaConstants.MediaTypes.Video]: MediaConstants.RouteNames.WatchVideo,
  [MediaConstants.MediaTypes.Image]: MediaConstants.RouteNames.ImagePreview,
  [MediaConstants.MediaTypes.Document]: MediaConstants.RouteNames.ReadDocument,
}

export default function ActionPickerContainer() {
  const navigation = useNavigation()
  const { currentItem, setCurrentItem } = useContext(FilesContext)
  const FileRename = useFileRenameContext()

  const onPickerClose = () => {
    setCurrentItem(undefined)
  }

  const onPick = ({ type, data }) => {
    ;({
      [Action.Open]: () => {
        onPickerClose()
        setTimeout(() => {
          navigation.navigate(...([Routes[data.type], { mediaId: data.id }] as never))
        })
      },
      [Action.ShowDetails]: () => console.log("show details"),
      [Action.Rename]: () => {
        onPickerClose()
        setTimeout(() => {
          FileRename.launch({ id: currentItem.id, name: currentItem.title })
        })
      },
      [Action.Download]: () => console.log("download"),
      [Action.MoveToTrash]: () => console.log("move to trash"),
    })[type]()
  }

  return (
    <Portal>
      {!!currentItem && (
        <ActionPicker
          visible={!!currentItem}
          title={currentItem.title}
          onDismiss={() => setCurrentItem(undefined)}
          onPick={(type) => onPick({ type, data: currentItem })}
        />
      )}
    </Portal>
  )
}
