import { StyleSheet } from "react-native"

export default StyleSheet.create({
  wrapper: {
    paddingHorizontal: 6,
  },
  scrollViewContent: {
    paddingTop: 0,
  },
  listItem: {
    borderTopLeftRadius: 100,
    borderRadius: 100,
  },
  listItemTitle: {
    fontSize: 14,
    fontFamily: "Roboto_400Regular",
  },
  brandAvatar: {
    backgroundColor: "transparent",
  },
  brandImage: {
    width: "100%",
    height: "100%",
  },
  drawerContainer: {
    flex: 1,
    paddingBottom: 12,
  },
  divider: {
    marginVertical: 8,
    marginHorizontal: 12,
  },
  version: {
    marginLeft: "auto",
    marginRight: "auto",
  },
  listSection: {
    marginVertical: 0,
    paddingVertical: 0,
    paddingHorizontal: 8,
  },
  listItemDescription: {
    fontSize: 12,
    fontFamily: "Roboto_400Regular",
  },
})
