import { StyleSheet, Dimensions } from "react-native"

const Window = Dimensions.get("window")

export default StyleSheet.create({
  pdf: {
    flex: 1,
    width: Window.width,
    height: Window.height,
    backgroundColor: "#000",
  },
})
