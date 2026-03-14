import { StyleSheet } from "react-native"

export default StyleSheet.create({
  keyboard: {
    flex: 1,
  },
  loading: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  tabs: {
    flexDirection: "row",
    justifyContent: "space-evenly",
    paddingVertical: 12,
  },
  tabButton: {
    flex: 1,
  },
  overview: {
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "center",
    gap: 10,
  },
  summary: {
    flexDirection: "column",
    alignItems: "center",
    gap: 4,
  },
  summaryContent: {
    alignItems: "center",
  },
  content: {
    flex: 1,
    gap: 24,
    paddingVertical: 12,
  },
})
