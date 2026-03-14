import { createContext } from "react"

import { UploadsState } from "../reducer"
import { Media } from "@domain/media/media/Media"

type UploadStateContextType = {
  uploadingItems: UploadsState["entities"]
}

export const UploadStateContext = createContext<UploadStateContextType>(
  {} as UploadStateContextType
)

type UploadDispatchContextType = {
  uploadAsync: (uri: string) => Promise<Media>
}

export const UploadDispatchContext = createContext<UploadDispatchContextType>(
  {} as UploadDispatchContextType
)
