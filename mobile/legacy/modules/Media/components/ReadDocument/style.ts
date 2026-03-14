import { StyleSheet, Dimensions } from "react-native"

const WINDOW_SIZE = Dimensions.get("window")

export default StyleSheet.create({
  header: {
    backgroundColor: "transparent",
  },
  container: {
    flex: 1,
    padding: 0,
    backgroundColor: "#000",
    alignItems: "center",
    justifyContent: "center",
  },
  pdf: {
    flex: 1,
    width: WINDOW_SIZE.width,
    height: WINDOW_SIZE.height,
    backgroundColor: "#000",
  },
})
