import { createTransformer } from "@infrastructure/utils/transformer"
import { MediaUser as MediaUserSchema } from "@infrastructure/schema/Media"
import { MediaUser } from "@domain/media/shares/MediaUser"

export const mediaUserTransformer = createTransformer<MediaUserSchema, MediaUser>((schema) => ({
  id: schema.id,
  urn: schema.urn,
  name: schema.name,
  email: schema.email,
  avatar: schema.photo,
  scopes: schema.scopes,
  isOwner: schema.isOwner,
  canRead: schema.canRead,
  canWrit: schema.canWrite,
}))
