import { selectAccessToken } from "auth-module/store"
import { store } from "core-module/store/configureStore"

import { MediaTypes } from "../../constants"

export type MediaSourcePropType = {
  uri: string
}

export type PrivateMediaSourcePropType = MediaSourcePropType & {
  headers: { Authorization: string }
}

export type MediaSourceProp = {
  source: MediaSourcePropType | MediaSourcePropType[]
}

type MediaSourceParamType = MediaSourcePropType | MediaSourcePropType[]
type PrivateMediaSourceType = PrivateMediaSourcePropType | PrivateMediaSourcePropType[]

type MediaType = (typeof MediaTypes)[keyof typeof MediaTypes]

export const isVideo = (type: MediaType): boolean => {
  return MediaTypes.Video === type
}

export const isDocument = (type: MediaType): boolean => {
  return MediaTypes.Document === type
}

export const isImage = (type: MediaType): boolean => {
  return MediaTypes.Image === type
}

export const formatBytes = (bytes, decimals = 2) => {
  if (bytes === 0) return "0 Bytes"

  const k = 1024,
    dm = decimals || 2,
    sizes = ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"],
    i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i]
}

export function withAuthorizationHeader(source: MediaSourcePropType): PrivateMediaSourcePropType {
  const authToken = selectAccessToken(store.getState())
  return {
    ...source,
    headers: {
      Authorization: `Bearer ${authToken}`,
    },
  } as PrivateMediaSourcePropType
}

export const transformToPrivateSource = (source: MediaSourceParamType): PrivateMediaSourceType => {
  return !Array.isArray(source)
    ? withAuthorizationHeader(source)
    : source.map(withAuthorizationHeader)
}

export const twoDigits = (num: number) => {
  return num.toString().padStart(2, "0")
}

export const convertMsToHM = (ms: number) => {
  let seconds = Math.floor(ms / 1000)
  let minutes = Math.floor(seconds / 60)
  let hours = Math.floor(minutes / 60)

  seconds = seconds % 60
  // 👇️ if seconds are greater than 30, round minutes up (optional)
  minutes = seconds >= 30 ? minutes + 1 : minutes

  minutes = minutes % 60

  // 👇️ If you don't want to roll hours over, e.g. 24 to 00
  // 👇️ comment (or remove) the line below
  // commenting next line gets you `24:00:00` instead of `00:00:00`
  // or `36:15:31` instead of `12:15:31`, etc.
  hours = hours % 24

  if (hours > 0) {
    return `${twoDigits(hours)}:${twoDigits(minutes)}:${twoDigits(seconds)}`
  } else {
    return `${twoDigits(minutes)}:${twoDigits(seconds)}`
  }
}

export default {
  isVideo,
  isDocument,
  isImage,
  formatBytes,
  convertMsToHM,
  withAuthorizationHeader,
  twoDigits,
  transformToPrivateSource,
}
