import { createTransformer } from "@infrastructure/utils/transformer"
import { User as UserSchema } from "@infrastructure/schema/User"
import { User } from "@domain/user/User"
import { teamTransformer } from "./TeamTransformer"

export const userTransformer = createTransformer<UserSchema, User>((schema) => ({
  id: schema.id,
  urn: schema.urn,
  fullName: schema.fullName,
  firstName: schema.firstName,
  lastName: schema.lastName,
  email: schema.email,
  photoURL: schema.photo,
  birthdate: schema.birthdate,
  gender: schema.gender,
  permissions: schema.permissions,
  currentTeam: schema.currentTeam
    ? {
        ...teamTransformer.transform(schema.currentTeam),
        membershipId: schema.currentTeam.membershipId,
      }
    : undefined,
}))
