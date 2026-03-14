import { StyleSheet } from "react-native"

export default StyleSheet.create({
  card: {
    marginVertical: 6,
    marginHorizontal: 12,
  },
  container: {
    minHeight: 72,
    paddingLeft: 16,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  titles: {
    flex: 1,
    flexDirection: "column",
    justifyContent: "center",
  },
  title: {
    minHeight: 30,
    paddingRight: 16,
  },
  progress: {
    justifyContent: "center",
    minHeight: 20,
    marginVertical: 0,
    paddingRight: 16,
  },
  left: {
    marginRight: 16,
    justifyContent: "center",
    height: 40,
    width: 40,
  },
})
