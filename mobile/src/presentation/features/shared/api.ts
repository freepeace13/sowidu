import { createApi } from "@reduxjs/toolkit/query/react"
import { permissionGroupService, teamAttributeService, apiClient } from "@application/main"
import { InstitutionType, LegalForm } from "@domain/teams/team/Team"
import { createSelector } from "@reduxjs/toolkit"
import { RootState } from "@presentation/app/store"

export const sharedApi = createApi({
  baseQuery: (config) =>
    apiClient
      .send(config)
      .then((data) => ({ data }))
      .catch((error) => ({ error })),
  tagTypes: ["User", "Roles", "TeamMember"],
  endpoints: (build) => ({
    getPermissions: build.query<{ label: string; permissions: string[] }[], void>({
      queryFn: () => {
        return permissionGroupService
          .getGroupedPermissions()
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    getInstitutionTypes: build.query<InstitutionType[], void>({
      queryFn: () => {
        return teamAttributeService
          .institutionTypes()
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),

    getLegalForms: build.query<LegalForm[], void>({
      queryFn: () => {
        return teamAttributeService
          .legalForms()
          .then((data) => ({ data }))
          .catch((error) => ({ error }))
      },
    }),
  }),
})

export const { getLegalForms, getInstitutionTypes } = sharedApi.endpoints

export const selectInstitutionTypes = createSelector(
  (state: RootState) => getInstitutionTypes.select()(state),
  (response) => response.data
)

export const selectLegalForms = createSelector(
  (state: RootState) => getLegalForms.select()(state),
  (response) => response.data
)

export const selectGetInstitutionTypesQueryState = createSelector(
  (state: RootState) => getInstitutionTypes.select()(state),
  (response) => response
)

export const { useGetPermissionsQuery, useGetInstitutionTypesQuery, useGetLegalFormsQuery } =
  sharedApi
