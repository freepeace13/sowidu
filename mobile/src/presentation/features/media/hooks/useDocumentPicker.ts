import * as DocumentPicker from "expo-document-picker"

export const useDocumentPicker = (
  type: string | string[] = [
    "application/pdf",
    "image/png",
    "image/jpeg",
    "video/quicktime",
    "video/mp4",
  ]
) => {
  return async () => {
    return await DocumentPicker.getDocumentAsync({
      type,
      copyToCacheDirectory: true,
      multiple: false,
    }).then((pickerResult) => {
      if (!pickerResult.canceled) {
        return pickerResult.assets[0]
      }
    })
  }
}
