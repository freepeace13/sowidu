import { PendingUpload, UploadId } from "./PendingUpload"

export type UploadProgressData = {
  totalBytesSent: number
  totalBytesExpectedToSend: number
}

export type UploadProgressCallback = (data: UploadProgressData) => void

export interface UploadRepository {
  find: (uuid: UploadId) => PendingUpload | undefined
  remove: (uuid: UploadId) => void
  create: (fileUri: string, onProgress?: UploadProgressCallback) => Promise<PendingUpload>
  cancel: (uuid: UploadId) => Promise<void>
}
