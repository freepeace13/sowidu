import { StyleSheet } from "react-native"

export default StyleSheet.create({
  content: {
    flex: 1,
    // paddingVertical: 6,
  },
  modal: {
    height: "100%",
    flex: 1,
  },
  modalContent: {
    flex: 1,
    paddingVertical: 16,
    paddingHorizontal: 22,
    margin: 12,
    borderRadius: 6,
  },
  modalContentWrapper: {
    flex: 1,
  },
  modalHeader: {
    marginBottom: 12,
    alignItems: "center",
    flexDirection: "row",
    justifyContent: "space-between",
  },
  modalForm: {
    flexDirection: "column",
    marginBottom: "auto",
    gap: 6,
  },
  member: {
    paddingHorizontal: 16,
    paddingVertical: 4,
  },
})
