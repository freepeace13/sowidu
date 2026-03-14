import * as Sentry from "@sentry/react-native"
import { useAuthToken } from "auth-module"
import { Constants as CoreConstants } from "core-module"
import * as FileSystem from "expo-file-system"
import { useState } from "react"

const API_UPLOAD_FILE_URL = CoreConstants.API_URL + "/media/upload"

export default function useMediaUploadRequest() {
  const token = useAuthToken()
  const [error, setError] = useState()
  const [progress, setProgress] = useState(0)
  const [isProcessing, setIsProcessing] = useState(false)

  const options: FileSystem.FileSystemUploadOptions = {
    fieldName: "file",
    httpMethod: "POST",
    uploadType: FileSystem.FileSystemUploadType.MULTIPART,
    // parameters: {
    //   last_modified: String(Date.now() / 1000),
    // },
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  }

  const uploadAsync = async (file) => {
    if (isProcessing) return

    setError(null)
    setProgress(0)
    setIsProcessing(true)

    const onProgress = ({ totalBytesExpectedToSend, totalBytesSent }) => {
      setProgress(Math.floor((totalBytesSent / totalBytesExpectedToSend) * 100))
    }

    try {
      await FileSystem.createUploadTask(
        API_UPLOAD_FILE_URL,
        file,
        options,
        onProgress,
      ).uploadAsync()
    } catch (err) {
      setError(err)
      Sentry.captureException(err)
      throw err
    } finally {
      setIsProcessing(false)
    }
  }

  return {
    uploadAsync,
    error,
    progress,
    isProcessing,
  }
}
