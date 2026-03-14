import { Team as TeamSchema } from "@infrastructure/schema/Team"
import { createTransformer } from "@infrastructure/utils/transformer"
import { Team } from "@domain/teams/team/Team"

export const teamTransformer = createTransformer<TeamSchema, Team>((schema) => ({
  id: schema.id,
  urn: schema.urn,
  name: schema.name,
  photoURL: schema.photo,
  institutionType: schema.institutionType,
  legalForm: schema.legalForm,
}))
