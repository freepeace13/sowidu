import { FunctionComponent, useRef, useState } from "react"

import { TouchableOpacity } from "react-native-gesture-handler"
import { CropView } from "react-native-image-crop-tools"
import * as ImageManipulator from "expo-image-manipulator"
import { Image, View } from "react-native"
import * as ImagePicker from "expo-image-picker"

import Style from "./AvatarPickerStyle"
import { Button, Text, Modal, Portal, useTheme } from "react-native-paper"

export type AvatarPickerResult = {
  uri: string
  name: string
  type: "image/png" | "image/jpeg"
}

interface AvatarPickerProps {
  uri: string
  onCropped: (result: AvatarPickerResult) => void
}

const AvatarPicker: FunctionComponent<AvatarPickerProps> = ({ uri, onCropped }) => {
  const { colors } = useTheme()
  const [imageUri, setImageUri] = useState<string>("")
  const [isCropperVisible, setIsCropperVisible] = useState(false)
  const cropViewRef = useRef<CropView>(null)

  const pickImage = async () => {
    const permissionResult = await ImagePicker.requestMediaLibraryPermissionsAsync()

    if (!permissionResult.granted) {
      alert("Permission to access gallery is required!")
      return
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      quality: 1,
    })

    if (!result.canceled) {
      setImageUri(result.assets[0].uri)
      setIsCropperVisible(true)
    }
  }

  const onCropImage = async (uri: string) => {
    setIsCropperVisible(false)

    const manipResult = await ImageManipulator.manipulateAsync(
      uri,
      [{ resize: { width: 300, height: 300 } }],
      { compress: 1, format: ImageManipulator.SaveFormat.PNG }
    )

    onCropped({
      uri: manipResult.uri,
      name: manipResult.uri.split("/").pop() || "avatar.png",
      type: "image/png",
    })
  }

  const onSave = () => {
    if (cropViewRef?.current) {
      cropViewRef.current.saveImage(true)
    }
  }

  const onDismiss = () => {
    setIsCropperVisible(false)
  }

  return (
    <View style={Style.container}>
      <TouchableOpacity onPress={pickImage}>
        {uri ? (
          <Image source={{ uri }} style={Style.avatar} />
        ) : (
          <View style={Style.placeholder}>
            <Text style={Style.placeholderText}>Select Avatar</Text>
          </View>
        )}
      </TouchableOpacity>
      <Button onPress={pickImage}>Change Avatar</Button>

      {isCropperVisible && (
        <Portal>
          <Modal
            visible
            dismissable={false}
            contentContainerStyle={{ flex: 1, backgroundColor: colors.onBackground }}
          >
            <View style={Style.wrapper}>
              <View style={Style.wrapper}>
                <CropView
                  sourceUrl={imageUri}
                  style={{ flex: 1 }}
                  ref={cropViewRef}
                  onImageCrop={({ uri }) => onCropImage(uri)}
                  keepAspectRatio
                  aspectRatio={{ width: 1, height: 1 }}
                />
              </View>
              <View style={Style.actionButtons}>
                <Button
                  mode="contained"
                  buttonColor={colors.background}
                  textColor={colors.onBackground}
                  onPress={onSave}
                  theme={{ roundness: 2 }}
                >
                  Save
                </Button>
                <Button textColor={colors.surface} onPress={onDismiss} theme={{ roundness: 2 }}>
                  Cancel
                </Button>
              </View>
            </View>
          </Modal>
        </Portal>
      )}
    </View>
  )
}

export default AvatarPicker
