import type { AVPlaybackSourceObject } from "expo-av"
import type { ImageURISource } from "react-native"
import type { Source as PDFSource } from "react-native-pdf"
import { MediaType } from "../types"

export type MediaSource = PDFSource | ImageURISource | AVPlaybackSourceObject

export interface MediaFile {
  name: string
  type: MediaType
  size: number
  source: MediaSource
  thumbnail: ImageURISource
  responsive?: ImageURISource[]
}
