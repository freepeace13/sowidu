import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { AVPlaybackSourceObject } from "expo-av"
import { useCallback } from "react"
import { ImageURISource } from "react-native"
import { Source as PDFSource } from "react-native-pdf"

// const forbiddenImage = require("../../../../../assets/images/temp-forbidden-image-placeholder.png")

type PrivateURiSource = ImageURISource | AVPlaybackSourceObject | PDFSource

interface PrivateURiHandler {
  <T extends PrivateURiSource>(source: T): T
}

interface UsePrivateURiSourceOptions {
  strict?: boolean
}

export const usePrivateURiSource = (options?: UsePrivateURiSourceOptions) => {
  const { accessToken, isGuest } = useAccount()

  const { strict } = Object.assign({
    strict: true,
    ...options,
  })

  const privateUriCallback = useCallback<PrivateURiHandler>(
    (source) => {
      if (isGuest && strict) {
        throw new Error("Cannot use private URi for guest users.")
      }

      return {
        ...source,
        headers: {
          ...source.headers,
          Authorization: `Bearer ${accessToken}`,
        },
      }
    },
    [isGuest, accessToken, strict]
  )

  return privateUriCallback
}
