import { StyleSheet } from "react-native"

export default StyleSheet.create({
  contentContainer: {
    flexGrow: 1,
    justifyContent: "flex-end",
  },
  inputContainer: {
    flexDirection: "row",
    alignItems: "center",
    padding: 8,
    // borderTopWidth: 1,
    // borderBottomWidth: 1,
  },
  input: {
    flexGrow: 1,
    borderTopLeftRadius: 6,
    borderTopRightRadius: 6,
    borderBottomRightRadius: 6,
    borderBottomLeftRadius: 6,
  },
  inputAvatar: {
    flexShrink: 1,
  },
})
