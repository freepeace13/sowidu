import * as ImagePicker from "expo-image-picker"

export const useImagePicker = () => {
  return async () => {
    const permissionResponse = await ImagePicker.requestMediaLibraryPermissionsAsync()
    if (permissionResponse.granted) {
      return await ImagePicker.launchImageLibraryAsync({
        allowsEditing: false,
        quality: 0.75,
        mediaTypes: ImagePicker.MediaTypeOptions.All,
        allowsMultipleSelection: false,
        videoMaxDuration: 60 * 10, // seconds
      }).then((imagePickerResult) => {
        if (!imagePickerResult.canceled) {
          return imagePickerResult.assets[0]
        }
      })
    }
  }
}
