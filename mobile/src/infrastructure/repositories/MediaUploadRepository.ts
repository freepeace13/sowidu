import { PendingUpload } from "@domain/media/upload/PendingUpload"
import { UploadRepository, UploadProgressData } from "@domain/media/upload/UploadRepository"
import { MEDIA_UPLOAD_URI } from "@infrastructure/config"
import { withAuthorizationHeader } from "@infrastructure/utils/withAuthorizationHeader"
import {
  FileSystemNetworkTaskProgressCallback,
  FileSystemUploadOptions,
  FileSystemUploadType,
  UploadTask as ExpoFilesystemUploadTask,
} from "expo-file-system"

export class UploadTask extends ExpoFilesystemUploadTask {
  public getId() {
    return this.uuid
  }

  public static make(
    fileUri: string,
    options?: FileSystemUploadOptions,
    callback?: FileSystemNetworkTaskProgressCallback<UploadProgressData>
  ): UploadTask {
    return new UploadTask(MEDIA_UPLOAD_URI, fileUri, options, callback)
  }
}

const options: FileSystemUploadOptions = {
  fieldName: "file",
  httpMethod: "POST",
  uploadType: FileSystemUploadType.MULTIPART,
  headers: {
    Accept: "application/json",
  },
}

const _tasks: PendingUpload[] = []

export const mediaUploadRepository: UploadRepository = {
  find(uuid) {
    return _tasks.find((task) => task.uuid === uuid)
  },

  async create(fileUri, onProgress) {
    const uploadConfig = await withAuthorizationHeader(options)
    const uploadTask = UploadTask.make(fileUri, uploadConfig, onProgress)

    const pendingUpload = new PendingUpload(
      uploadTask.getId(),
      uploadTask.uploadAsync(),
      uploadTask.cancelAsync
    )

    _tasks.push(pendingUpload)

    return pendingUpload
  },

  remove(uuid) {
    const index = _tasks.findIndex((i) => i.uuid === uuid)

    if (index !== -1) {
      _tasks.splice(index, 1)
    }
  },

  async cancel(uuid) {
    const task = this.find(uuid)

    if (task) {
      ;(await task).cancel()
    }
  },
}
