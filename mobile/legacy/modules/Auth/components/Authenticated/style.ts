import { StyleSheet } from "react-native"

export default StyleSheet.create({
  screen: {
    flex: 1,
    alignItems: "stretch",
    justifyContent: "space-between",
  },
  logoContainer: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  logo: {
    width: 108,
    height: 108,
  },
  spinnerContainer: {
    marginLeft: "auto",
    marginRight: "auto",
    paddingVertical: 28,
  },
  fetchingIndicator: {
    flex: 1,
    height: "100%",
    alignItems: "center",
    justifyContent: "center",
    rowGap: 12,
  },
})
