import { PermissionGroupRepository } from "@domain/auth/PermissionGroupRepository"
import { PermissionGroup } from "@domain/auth/PermissionGroup"
import { PermissionGroup as PermissionGroupSchema } from "@infrastructure/schema/PermissionGroup"
import { request } from "@infrastructure/api/request"
import { GetPermissionsByGroupUri } from "@infrastructure/api/urls"
import { permissionGroupTransformer } from "@infrastructure/transformers/PermissionGroupTransformer"

export const permissionGroupRepository: PermissionGroupRepository = {
  getPermissionsByGroup: async () => {
    return await request<PermissionGroupSchema[], PermissionGroup[]>({
      url: GetPermissionsByGroupUri.replace({}),
      method: "GET",
      transformResponse: ({ data }) => permissionGroupTransformer.collection(data),
    })
  },
}
