import { mediaService, mediaUploadService } from "@application/main"
import { UploadId } from "@domain/media/upload/PendingUpload"
import { UploadProgressData } from "@domain/media/upload/UploadRepository"
import { createAction } from "@reduxjs/toolkit"

export const uploadCreated = createAction<{
  uuid: string
  uri: string
  timestamp: number
}>("upload/created")

export const progressReceived = createAction<{
  uuid: string
  progress: UploadProgressData
}>("upload/progress")

export const uploadCompleted = createAction<{
  uuid: string
}>("upload/completed")

export const uploadFileUriAsync = (uri: string) => {
  let uuid: string
  return async (dispatch: any, _: any) => {
    const onCreated = (uploadId: UploadId): void => {
      uuid = uploadId
      dispatch(uploadCreated({ uuid, uri, timestamp: Date.now() }))
    }

    const onProgress = (progress: UploadProgressData): void => {
      if (!uuid) return
      dispatch(progressReceived({ uuid, progress }))
    }

    try {
      const pendingTask = await mediaUploadService.create(uri, onProgress)
      onCreated(pendingTask.uuid)
      const media = await mediaService.upload(pendingTask)
      dispatch(uploadCompleted({ uuid }))
      return media
    } catch (e) {
      console.error(JSON.stringify(e))
      throw e
    }
  }
}
