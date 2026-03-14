import { FunctionComponent, ReactNode, useMemo, useReducer } from "react"

import { uploadFileUriAsync } from "../actions"
import { UploadStateContext, UploadDispatchContext } from "../contexts/UploadContexts"
import { initialState, uploadsReducer } from "../reducer"

interface UploadContextProviderProps {
  children: ReactNode
}

const UploadContextProvider: FunctionComponent<UploadContextProviderProps> = ({ children }) => {
  const [state, dispatch] = useReducer(uploadsReducer, initialState)

  const uploadAsync = (uri: string) => {
    return uploadFileUriAsync(uri)(dispatch, () => state)
  }

  const uploadingItems = useMemo(() => state.entities, [state])

  return (
    <UploadStateContext.Provider value={{ uploadingItems }}>
      <UploadDispatchContext.Provider value={{ uploadAsync }}>
        {children}
      </UploadDispatchContext.Provider>
    </UploadStateContext.Provider>
  )
}

export default UploadContextProvider
