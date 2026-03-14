import { AttributeValueRepository } from "@domain/teams/team/AttributeValueRepository"
import { InstitutionType, LegalForm } from "@domain/teams/team/Team"
import { GetInstitutionTypesUri, GetLegalFormsUri } from "@infrastructure/api/urls"
import { request } from "@infrastructure/api/request"

export const teamAttributeValueRepository: AttributeValueRepository = {
  async institutionTypes() {
    return await request<{ institutions: InstitutionType[] }, InstitutionType[]>({
      url: GetInstitutionTypesUri.replace({}),
      method: "GET",
      transformResponse: ({ data }) => data.institutions,
    })
  },

  async legalForms() {
    return await request<{ legalForms: LegalForm[] }, LegalForm[]>({
      url: GetLegalFormsUri.replace({}),
      method: "GET",
      transformResponse: ({ data }) => data.legalForms,
    })
  },
}
