import BottomSheetMenuItem from "@presentation/components/BottomSheetMenu/BottomSheetMenuItem"
import BottomSheetMenuLayout, {
  ContentProps,
} from "@presentation/components/BottomSheetMenu/BottomSheetMenuLayout"
import { useUploadContext } from "@presentation/features/media/features/uploads/hooks/useUploadContext"
import { useDocumentPicker } from "@presentation/features/media/hooks/useDocumentPicker"
import { useCallback } from "react"
import { IconButton, useTheme } from "react-native-paper"

import Style from "./FileUploadButtonStyle"
import { useAppDispatch } from "@presentation/app/store/hooks"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { useInfiniteMediaFiles } from "../../contexts/InfiniteMediaFilesContext"
import { fileUploaded } from "../../actions"
import { Media } from "@domain/media/media/Media"
import { useImagePicker } from "../../hooks/useImagePicker"

function FileUploadButton() {
  const dispatch = useAppDispatch()
  const { colors } = useTheme()
  const pickDocument = useDocumentPicker()
  const pickMediaLibrary = useImagePicker()
  const { uploadAsync, uploadingItems } = useUploadContext()
  const flashMessage = useFlashMessage()
  const { prependItem } = useInfiniteMediaFiles()

  const uploadCompleted = useCallback(
    (uploadedMedia: Media) => {
      prependItem(uploadedMedia)
      dispatch(fileUploaded(uploadedMedia))

      if (uploadingItems.length === 0) {
        flashMessage.showMessage("Upload successfully completed.")
      }
    },
    [flashMessage, uploadingItems, dispatch, prependItem]
  )

  const launchDocumentPicker = useCallback(
    async (contentProps: ContentProps) => {
      contentProps.onDismiss()
      requestAnimationFrame(async () => {
        const result = await pickDocument()
        if (result) {
          const media = await uploadAsync(result.uri)
          uploadCompleted(media)
        }
      })
    },
    [uploadCompleted, pickDocument, uploadAsync]
  )

  const launchMediaLibrary = useCallback(
    async (contentProps: ContentProps) => {
      contentProps.onDismiss()
      requestAnimationFrame(async () => {
        const result = await pickMediaLibrary()
        if (result) {
          const media = await uploadAsync(result.uri)
          uploadCompleted(media)
        }
      })
    },
    [uploadCompleted, pickMediaLibrary, uploadAsync]
  )

  const renderContent = useCallback<(content: ContentProps) => React.JSX.Element>(
    (contentProps) => (
      <>
        <BottomSheetMenuItem
          title="Browse"
          icon="folder-open"
          onPress={() => launchDocumentPicker(contentProps)}
        />
        <BottomSheetMenuItem
          title="Photos or Video"
          icon="video-image"
          onPress={() => launchMediaLibrary(contentProps)}
        />
        <BottomSheetMenuItem title="Camera" icon="camera" />
      </>
    ),
    [launchDocumentPicker, launchMediaLibrary]
  )

  return (
    <BottomSheetMenuLayout content={renderContent}>
      {(childrenProps) => (
        <IconButton
          icon="plus"
          mode="contained"
          animated
          iconColor={colors.onPrimary}
          containerColor={colors.primary}
          onPress={childrenProps.onPresent}
          size={28}
          style={Style.button}
        />
      )}
    </BottomSheetMenuLayout>
  )
}

export default FileUploadButton
