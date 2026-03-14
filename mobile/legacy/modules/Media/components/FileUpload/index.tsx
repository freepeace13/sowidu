import * as DocumentPicker from "expo-document-picker"
import * as ImagePicker from "expo-image-picker"
import { useEffect, useState } from "react"
import { View } from "react-native"
import { ActivityIndicator, Snackbar, Text, useTheme } from "react-native-paper"
import { Loading } from "ui-module"

import UploadActionPicker from "./UploadActionPicker"
import Style from "./style"
import * as MediaConstants from "../../constants"
import { useMediaUploadRequest } from "../../hooks"
import CameraModal from "../CameraModal"

interface Props {
  onComplete: (response: any) => void
  onStart?: (uri: any) => void
  onError?: (error) => void
}

export default function UploadButtonWithProgress(props: Props) {
  const { colors } = useTheme()
  const [isCameraOpen, setIsCameraOpen] = useState(false)
  const { uploadAsync, isProcessing, error } = useMediaUploadRequest()

  useEffect(() => {
    if (error) {
      props.onError && props.onError(error)
    }
  }, [error])

  const uploadFileAsync = async (uri) => {
    props.onStart && props.onStart(uri)
    const response = await uploadAsync(uri)
    props.onComplete && props.onComplete(response)
  }

  const onBrowse = async () => {
    const pickerResult = await DocumentPicker.getDocumentAsync({
      type: MediaConstants.MimeTypes,
      copyToCacheDirectory: true,
      multiple: false,
    })

    if (!pickerResult.canceled) {
      const asset = pickerResult.assets[0]
      uploadFileAsync(asset.uri)
    }
  }

  const onMediaLibrary = async () => {
    const permissionResponse = await ImagePicker.requestMediaLibraryPermissionsAsync()

    if (permissionResponse.granted) {
      const result = await ImagePicker.launchImageLibraryAsync({
        allowsEditing: false,
        quality: 0.75,
        mediaTypes: ImagePicker.MediaTypeOptions.All,
        allowsMultipleSelection: false,
        videoMaxDuration: MediaConstants.VideoMaxDurationSeconds,
      })

      if (!result.canceled) {
        const asset = result.assets[0]
        uploadFileAsync(asset.uri)
      }
    }
  }

  const onCamera = () => {
    setIsCameraOpen(true)
  }

  const onCameraSuccess = (response) => {
    uploadFileAsync(response.source.uri)
    onCameraClose()
  }

  const onCameraClose = () => {
    setIsCameraOpen(false)
  }

  const renderCameraModal = () => {
    if (isCameraOpen) {
      return (
        <CameraModal onClose={onCameraClose} onSuccess={onCameraSuccess} onError={console.error} />
      )
    }
  }

  const renderLoading = () => {
    return (
      <>
        <View style={Style.loadingBackdrop} />
        <Snackbar visible onDismiss={console.log}>
          <View style={{ flexDirection: "row", alignItems: "center" }}>
            <ActivityIndicator color={colors.primary} style={{ marginRight: 12 }} />
            <Text style={{ color: colors.surfaceVariant }}>Uploading</Text>
          </View>
        </Snackbar>
      </>
    )
  }

  return (
    <>
      {renderCameraModal()}
      {isProcessing && renderLoading()}
      {!isProcessing && (
        <UploadActionPicker
          onBrowsePress={onBrowse}
          onCameraPress={onCamera}
          onPhotoOrVideoPress={onMediaLibrary}
        />
      )}
    </>
  )
}
