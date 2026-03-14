import { createTransformer } from "@infrastructure/utils/transformer"
import { Media as MediaSchema } from "@infrastructure/schema/Media"
import { Media } from "@domain/media/media/Media"
import { MediaType } from "@domain/media/types"

export const mediaTransformer = createTransformer<MediaSchema, Media>((schema) => ({
  id: schema.id,
  title: schema.title,
  file: {
    name: schema.file.name,
    type: schema.file.type,
    size: schema.file.size,
    source: { uri: schema.file.uri },
    thumbnail: { uri: schema.file.thumbnail },
    responsive: MediaType.Image === schema.file.type ? schema.file.responsive : undefined,
  },
  shared: schema.shared,
  uploadDate: schema.uploadDate,
  permission: schema.policy,
}))
