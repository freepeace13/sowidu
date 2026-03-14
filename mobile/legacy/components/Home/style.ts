import { StyleSheet } from "react-native"

export default StyleSheet.create({
  content: {
    flex: 1,
    rowGap: 10,
    paddingHorizontal: 16,
    paddingVertical: 14,
  },
  divider: {
    marginVertical: 6,
    backgroundColor: "transparent",
  },
  headerImage: {
    width: 121,
    height: 28,
  },
  listItem: {
    borderWidth: 1,
    borderRadius: 15,
    borderColor: "#C0C7CD",
    paddingHorizontal: 16,
  },
  listIconContainer: {
    padding: 8,
    alignItems: "center",
    justifyContent: "center",
    borderCurve: "circular",
    borderRadius: 100,
  },
})
