import { useState, useContext, createContext } from "react"
import { View } from "react-native"
import { Text, Divider, Button, TextInput, Portal } from "react-native-paper"
import { BottomSheet } from "ui-module"

import Style from "./style"
import { Api as MediaApi } from "../../services"

type FileInfo = {
  id: string
  name: string
}

type Context = {
  shown: boolean
  launch: (target: FileInfo) => void
  dismiss: () => void
}

const FileRenameContext = createContext({} as Context)

export const useFileRenameContext = () => useContext(FileRenameContext)

export default function FileRenameContainer({ children }) {
  const [isShown, setIsShown] = useState(false)
  const [info, setInfo] = useState<FileInfo>()

  const [updateMediaDetails, { isLoading, isError }] = MediaApi.useUpdateMediaDetailsMutation()

  const onSavePress = () => {
    updateMediaDetails({
      id: info.id,
      details: {
        name: info.name,
      },
    }).then(() => {
      onDismiss()
    })
  }

  const onLaunch = (target: FileInfo) => {
    setInfo(target)
    setIsShown(true)
  }

  const onDismiss = () => {
    setInfo(null)
    setIsShown(false)
  }

  const state = {
    shown: isShown,
    launch: onLaunch,
    dismiss: onDismiss,
  }

  return (
    <FileRenameContext.Provider value={state}>
      {children}
      <Portal>
        {isShown && (
          <FileRenameForm
            isOpen={isShown}
            onCancel={onDismiss}
            isError={isError}
            isProcessing={isLoading}
            value={info?.name}
            onValueChange={(name) => setInfo((info) => ({ ...info, name }))}
            onSave={onSavePress}
          />
        )}
      </Portal>
    </FileRenameContext.Provider>
  )
}

interface Props {
  isOpen: boolean
  isError: boolean
  onCancel: () => void
  value: string
  isProcessing: boolean
  onValueChange: (value: any) => void
  onSave: () => void
}

function FileRenameForm({
  isOpen,
  onCancel,
  value,
  onValueChange,
  isProcessing,
  isError,
  onSave,
}: Props) {
  return (
    <BottomSheet
      onClose={() => {
        console.log("closed")
      }}
      isOpen={isOpen}
      backdropPressBehavior="none"
      enableOverDrag={false}
      enableBackdropTouch={false}
    >
      <BottomSheet.View>
        <View style={Style.titleContainer}>
          <Text variant="titleSmall" numberOfLines={1}>
            Rename
          </Text>
        </View>
        <Divider />
        <View style={Style.textInputContainer}>
          <TextInput
            label="New name"
            mode="outlined"
            disabled={isProcessing}
            value={value}
            onChangeText={onValueChange}
          />
        </View>
        <View style={Style.actionContainer}>
          <Button mode="contained" onPress={onSave} disabled={isProcessing} loading={isProcessing}>
            Save
          </Button>
          <Button mode="outlined" onPress={onCancel} disabled={isProcessing}>
            Cancel
          </Button>
        </View>
      </BottomSheet.View>
    </BottomSheet>
  )
}
