import { TeamRole } from "@infrastructure/schema/TeamRole"
import { createTransformer } from "@infrastructure/utils/transformer"
import { Role } from "@domain/teams/permissions/Role"

export const teamRoleTransformer = createTransformer<TeamRole, Role>((schema) => ({
  id: schema.id,
  teamId: schema.teamId,
  name: schema.name,
  permissions: schema.permissions,
}))
