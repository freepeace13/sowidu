import { PendingUpload, UploadId } from "./upload/PendingUpload"
import { UploadProgressCallback, UploadRepository } from "./upload/UploadRepository"

interface IMediaUploadService {
  find: (uuid: UploadId) => PendingUpload | undefined
  remove: (uuid: UploadId) => void
  create: (fileUri: string, onProgress?: UploadProgressCallback) => Promise<PendingUpload>
  cancel: (uuid: UploadId) => Promise<void>
}

export const mediaUploadService = (uploadRepository: UploadRepository): IMediaUploadService => ({
  find(uuid: UploadId): PendingUpload | undefined {
    return uploadRepository.find(uuid)
  },

  remove(uuid: UploadId): void {
    return uploadRepository.remove(uuid)
  },

  create(uri: string, progressCallback?: UploadProgressCallback): Promise<PendingUpload> {
    return uploadRepository.create(uri, progressCallback)
  },

  cancel(uuid: UploadId): Promise<void> {
    return uploadRepository.cancel(uuid)
  },
})
