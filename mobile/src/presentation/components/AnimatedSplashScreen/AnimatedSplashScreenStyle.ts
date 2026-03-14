import { StyleSheet } from "react-native"
import Constants from "expo-constants"

const BACKGROUND_COLOR = Constants?.expoConfig?.splash?.backgroundColor || "#006686"
const IMAGE_RESIZE_MODE = Constants?.expoConfig?.splash?.resizeMode || "contain"

export default StyleSheet.create({
  wrapper: { ...(StyleSheet.absoluteFill as object), backgroundColor: BACKGROUND_COLOR },
  logo: {
    width: "100%",
    height: "100%",
    resizeMode: IMAGE_RESIZE_MODE,
  },
})
