import { Api as CoreApi } from "core-module"
import { useAppDispatch } from "core-module/store/hooks"
import * as DocumentPicker from "expo-document-picker"
import * as FileSystem from "expo-file-system"
import * as ImagePicker from "expo-image-picker"
import { useContext, useEffect, useState } from "react"
import { FAB, useTheme } from "react-native-paper"
import { BottomSheetMenu } from "ui-module"

import CameraModal from "./CameraModal"
import Style from "./style"
import * as MediaConstants from "../../../../constants"
import { useMediaUploadRequest } from "../../../../hooks"
import { FilesContext } from "../Context"

const ImagePickerOptions = {
  allowsEditing: false,
  quality: 0.75,
  mediaTypes: ImagePicker.MediaTypeOptions.All,
  allowsMultipleSelection: false,
  videoMaxDuration: MediaConstants.VideoMaxDurationSeconds,
}

const DocumentPickerOptions = {
  type: MediaConstants.MimeTypes,
  copyToCacheDirectory: true,
  multiple: false,
}

enum Action {
  MediaLibrary = "MEDIA_LIBRARY",
  Camera = "CAMERA",
  Browse = "BROWSE",
}

const Actions: { label: string; value: Action; icon: string }[] = [
  {
    label: "Photo or Video",
    value: Action.MediaLibrary,
    icon: "video-image",
  },
  {
    label: "Camera",
    value: Action.Camera,
    icon: "camera",
  },
  {
    label: "Browse",
    value: Action.Browse,
    icon: "folder-open",
  },
]

interface Props {
  onPick: (type: Action) => void
}

function ActionPicker({ onPick }: Props) {
  const { colors } = useTheme()

  const renderTrigger = (props) =>
    !props.opened && (
      <FAB
        {...props}
        icon="plus"
        backgroundColor={colors.primary}
        color={colors.onPrimary}
        style={Style.fab}
      />
    )

  return (
    <BottomSheetMenu height={200} trigger={renderTrigger}>
      <BottomSheetMenu.Content>
        <BottomSheetMenu.Section>
          {Actions.map((i) => (
            <BottomSheetMenu.Item
              key={i.value}
              left={(props) => <BottomSheetMenu.Icon {...props} icon={i.icon} />}
              title={i.label}
              onPress={() => onPick(i.value)}
            />
          ))}
        </BottomSheetMenu.Section>
      </BottomSheetMenu.Content>
    </BottomSheetMenu>
  )
}

export default function UploadButton() {
  const { uploadItem, setUploadItem } = useContext(FilesContext)
  const { uploadAsync } = useMediaUploadRequest()
  const [isCameraOpen, setIsCameraOpen] = useState(false)
  const dispatch = useAppDispatch()

  useEffect(() => {
    function upload() {
      uploadAsync(uploadItem.uri)
        .then(() => {
          // dispatch(CoreApi.util.invalidateTags([{ type: "MediaModuleFiles", id: "LIST" }]))
        })
        .catch((err) => {
          console.error({ ...err })
        })
        .finally(() => {
          setUploadItem(undefined)
        })
    }
    uploadItem && upload()
  }, [uploadItem])

  const onPick = (action) => {
    const handlers = {
      [Action.Camera]: () => setIsCameraOpen(true),
      [Action.MediaLibrary]: () => openImagePicker(),
      [Action.Browse]: () => openDocumentPicker(),
    }
    if (!handlers[action]) {
      throw new Error(`Action handler for "${action}" not defined.`)
    }
    handlers[action]()
  }

  const prepareUploadItem = async (uri: string) => {
    const info = await FileSystem.getInfoAsync(uri)
    if (info.exists) {
      setUploadItem({
        uri: info.uri,
        name: info.uri.replace(/^.*[\\/]/, ""),
        size: info.size,
      })
    }
  }

  const openImagePicker = async () => {
    const permissionResponse = await ImagePicker.requestMediaLibraryPermissionsAsync()
    if (permissionResponse.granted) {
      const result = await ImagePicker.launchImageLibraryAsync(ImagePickerOptions)
      if (!result.canceled) {
        const asset = result.assets[0]
        prepareUploadItem(asset.uri)
      }
    }
  }

  const openDocumentPicker = async () => {
    const pickerResult = await DocumentPicker.getDocumentAsync(DocumentPickerOptions)
    if (!pickerResult.canceled) {
      const asset = pickerResult.assets[0]
      prepareUploadItem(asset.uri)
    }
  }

  const onCameraCapture = async ({ source }) => {
    await prepareUploadItem(source.uri)
    setIsCameraOpen(false)
  }

  return (
    <>
      <ActionPicker onPick={onPick} />
      {isCameraOpen && (
        <CameraModal
          onError={console.log}
          onClose={() => setIsCameraOpen(false)}
          onSuccess={onCameraCapture}
        />
      )}
    </>
  )
}

export { default as UploadIndicator } from "./UploadIndicator"
