import { FormDialog } from "@presentation/components"
import { FunctionComponent, createRef, useLayoutEffect, useState } from "react"
import { Keyboard, View, TextInput as RNTextInput } from "react-native"
import { HelperText, TextInput } from "react-native-paper"
import { useUpdateFileInfoMutation } from "../../mediaApi"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { Media } from "@domain/media/media/Media"
import { getValidationErrorMessage } from "@application/services/helpers"

interface FileRenamerProps {
  media: Media | undefined
  visible: boolean
  onDismiss: () => void
}

const FileRenamer: FunctionComponent<FileRenamerProps> = ({ media, visible, onDismiss }) => {
  const [fileName, setFileName] = useState(() => media?.title)
  const [updateFileInfo, { error, isLoading }] = useUpdateFileInfoMutation()
  const inputRef = createRef<RNTextInput>()

  useLayoutEffect(() => {
    requestAnimationFrame(() => inputRef?.current?.focus())
  }, [inputRef])

  const renderError = (name: string) => {
    const err = getValidationErrorMessage(error, name)
    return err && <HelperText type="error">{err}</HelperText>
  }

  const onSubmit = async () => {
    if (!media) return

    try {
      await updateFileInfo({
        name: fileName as string,
        mediaId: media.id,
      }).unwrap()
      onDismiss()
    } catch (error) {
      console.log(error)
    }
  }

  return (
    <FormDialog
      title="Rename"
      submitButtonText="Save"
      visible={visible}
      isLoading={isLoading}
      onDismiss={onDismiss}
      onSubmit={onSubmit}
    >
      <View>
        <TextInput
          dense
          selectTextOnFocus
          mode="outlined"
          onSubmitEditing={Keyboard.dismiss}
          value={fileName}
          onChangeText={setFileName}
          disabled={isLoading}
          error={!!getValidationErrorMessage(error, "name")}
          ref={inputRef}
        />
        {renderError("name")}
      </View>
    </FormDialog>
  )
}

export default FileRenamer
