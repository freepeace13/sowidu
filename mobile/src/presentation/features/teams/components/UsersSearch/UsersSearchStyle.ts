import { StyleSheet } from "react-native"

export default StyleSheet.create({
  modal: {
    flex: 1,
    margin: 8,
    borderRadius: 6,
  },
  wrapper: {
    flex: 1,
    borderRadius: 8,
  },
  header: {
    marginBottom: 16,
    alignItems: "center",
    flexDirection: "row",
    justifyContent: "space-between",
    marginHorizontal: 12,
    marginTop: 18,
  },
  title: {
    flexDirection: "row",
    gap: 12,
    alignItems: "center",
    marginLeft: 10,
  },
  searches: {
    flexDirection: "column",
    gap: 12,
    flex: 1,
    marginBottom: 12,
  },
  textInput: {
    marginHorizontal: 14,
  },
  emptyText: {
    textAlign: "center",
  },
})
