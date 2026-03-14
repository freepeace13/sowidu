import { createTransformer } from "@infrastructure/utils/transformer"
import { PermissionGroup as PermissionGroupSchema } from "@infrastructure/schema/PermissionGroup"
import { PermissionGroup } from "@domain/auth/PermissionGroup"

export const permissionGroupTransformer = createTransformer<PermissionGroupSchema, PermissionGroup>(
  (schema) => ({
    label: schema.label,
    permissions: [...schema.permissions],
  })
)
