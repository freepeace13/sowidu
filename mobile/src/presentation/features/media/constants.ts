import { MediaType } from "@domain/media/types"

export const colors = {
  [MediaType.Video]: "red",
  [MediaType.Image]: "green",
  [MediaType.Pdf]: "orange",
}

export const icons = {
  [MediaType.Video]: "video",
  [MediaType.Image]: "image",
  [MediaType.Pdf]: "file-pdf-box",
}
