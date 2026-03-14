import { StyleSheet } from "react-native"

export default StyleSheet.create({
  page: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: "space-between",
  },
  control: {
    paddingVertical: 18,
    flexDirection: "row",
    width: "100%",
    alignItems: "center",
    justifyContent: "space-around",
  },
})
