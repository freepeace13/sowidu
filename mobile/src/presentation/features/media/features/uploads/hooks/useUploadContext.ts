import { useContext } from "react"

import { UploadStateContext, UploadDispatchContext } from "../contexts/UploadContexts"

export const useUploadContext = () => {
  const state = useContext(UploadStateContext)
  const dispatch = useContext(UploadDispatchContext)

  if (!state || !dispatch) {
    throw new Error("Ensure that this component is wrapped by UploadContextProvider component.")
  }

  return {
    uploadingItems: state.uploadingItems,
    uploadAsync: dispatch.uploadAsync,
  }
}
