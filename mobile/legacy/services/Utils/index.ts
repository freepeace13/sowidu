import * as Updates from "expo-updates"

import { ExtendedEdge, useSafeAreaInsetsStyle } from "./useSafeAreaInsetsStyle"
import { ValidationError, isValidationError } from "../Api"

export const isDev = () => {
  return ["production", "staging"].includes(Updates.channel) === false
}

export function doubleTap(callback, timeout = 500) {
  let timer = null
  return (...args) => {
    if (timer) {
      clearTimeout(timer)
      timer = null
      return callback(...args)
    }
    clearTimeout(timer)
    timer = setTimeout(() => {
      timer = null
    }, timeout)
  }
}

export function inputValidationError(name: string, error: any) {
  if (isValidationError(error)) {
    const err = error as ValidationError
    const messages = err.data.errors[name]
    if (Array.isArray(messages) && messages.length > 0) {
      return messages[0]
    }
  }
}

export { ExtendedEdge, useSafeAreaInsetsStyle }

export default {
  isDev,
  doubleTap,
  inputValidationError,
  useSafeAreaInsetsStyle,
}
