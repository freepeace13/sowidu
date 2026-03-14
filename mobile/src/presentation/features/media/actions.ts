import { Media } from "@domain/media/media/Media"
import { createAction } from "@reduxjs/toolkit"

export const fileUploaded = createAction<Media>("media/fileUploaded")
