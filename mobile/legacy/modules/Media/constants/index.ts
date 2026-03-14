const MEDIA_IMAGE = "image"
const MEDIA_DOCUMENT = "pdf"
const MEDIA_VIDEO = "video"

export const VideoMaxDurationSeconds = 60 * 10
export const ImageQuality = 1

export const MediaTypes = {
  Image: MEDIA_IMAGE as typeof MEDIA_IMAGE,
  Document: MEDIA_DOCUMENT as typeof MEDIA_DOCUMENT,
  Video: MEDIA_VIDEO as typeof MEDIA_VIDEO,
}

export const MimeTypes = [
  "application/pdf",
  "image/png",
  "image/jpeg",
  "video/quicktime",
  "video/mp4",
]

export const MediaTypeIcons = {
  [MediaTypes.Image]: "image",
  [MediaTypes.Video]: "video",
  [MediaTypes.Document]: "file-pdf-box",
}

export const MediaTypeColors = {
  [MediaTypes.Image]: "green",
  [MediaTypes.Document]: "red",
  [MediaTypes.Video]: "orange",
}

export const RouteNames = {
  MediaNavigator: "MediaNavigator",
  GalleryNavigator: "GalleryNavigator",
  Gallery: {
    Files: "FilesScreen",
    Trash: "TrashScreen",
  },
  WatchVideo: "WatchVideoScreen",
  ImagePreview: "ImagePreviewScreen",
  ReadDocument: "ReadDocumentScreen",
}

export default {
  MediaTypes,
  RouteNames,
  MimeTypes,
  MediaTypeIcons,
  MediaTypeColors,
  VideoMaxDurationSeconds,
}
