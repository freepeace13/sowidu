import { createContext, useState } from "react"
import { ImageSourcePropType } from "react-native"

import * as MediaConstants from "../../../constants"

export enum ListType {
  Grid = "grid",
  List = "list",
}

export type ItemType = (typeof MediaConstants.MediaTypes)[keyof typeof MediaConstants.MediaTypes]

export interface Item {
  id: string
  title: string
  type: ItemType
  subtitle: string
  coverPhoto?: ImageSourcePropType
  coverPhotoHidden?: boolean
}

export interface ItemUpload {
  uri: any
  name?: string
  size?: number
}

type Context = {
  listType: ListType
  setListType: (value: ListType) => void
  currentItem: undefined | Item
  setCurrentItem: (item: Item) => void
  uploadItem: undefined | ItemUpload
  setUploadItem: (item: ItemUpload) => void
}

export const FilesContext = createContext({} as Context)

export function FilesContextProvider({ children }) {
  const [listType, setListType] = useState(ListType.List)
  const [currentItem, setCurrentItem] = useState(undefined)
  const [uploadItem, setUploadItem] = useState(undefined)

  return (
    <FilesContext.Provider
      value={{
        listType,
        setListType,
        currentItem,
        setCurrentItem,
        uploadItem,
        setUploadItem,
      }}
    >
      {children}
    </FilesContext.Provider>
  )
}
