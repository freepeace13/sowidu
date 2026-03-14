import { StyleSheet } from "react-native"

export default StyleSheet.create({
  container: {
    // borderWidth: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  wrapper: {
    flex: 1,
  },
  avatar: {
    width: 150,
    height: 150,
    borderRadius: 75,
  },
  placeholder: {
    width: 150,
    height: 150,
    borderRadius: 75,
    backgroundColor: "#ccc",
    justifyContent: "center",
    alignItems: "center",
  },
  placeholderText: {
    color: "#fff",
    fontSize: 16,
  },
  actionButtons: {
    gap: 16,
    paddingHorizontal: 12,
    paddingVertical: 28,
  },
})
