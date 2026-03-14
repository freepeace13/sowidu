import { StyleSheet, Dimensions } from "react-native"

const WindowSize = Dimensions.get("window")

export default StyleSheet.create({
  header: {
    backgroundColor: "transparent",
  },
  container: {
    height: WindowSize.height,
    width: WindowSize.width,
    flexShrink: 1,
  },
  image: {
    width: "100%",
    height: "100%",
  },
})
