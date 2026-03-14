import { FunctionComponent } from "react"
import { Media } from "@domain/media/media/Media"
import { Button, Dialog, Text, useTheme } from "react-native-paper"

interface FileDetailsProps {
  media: Media | undefined
  visible: boolean
  onDismiss: () => void
}

const FileDetails: FunctionComponent<FileDetailsProps> = ({ media, visible, onDismiss }) => {
  const { colors } = useTheme()
  return media ? (
    <Dialog
      visible={visible}
      onDismiss={onDismiss}
      theme={{ roundness: 1 }}
      style={{ backgroundColor: colors.background }}
    >
      <Dialog.Title>File Information</Dialog.Title>
      <Dialog.Content>
        <Text>FileName: {media.title}</Text>
      </Dialog.Content>
      <Dialog.Actions>
        <Button mode="text" onPress={onDismiss} contentStyle={{ paddingHorizontal: 12 }}>
          Done
        </Button>
      </Dialog.Actions>
    </Dialog>
  ) : null
}

export default FileDetails
