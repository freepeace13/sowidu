import { AttributeValueRepository } from "./team/AttributeValueRepository"
import { LegalForm } from "./team/Team"

import { InstitutionType } from "./team/Team"

export interface IAttributeValueService {
  institutionTypes: () => Promise<InstitutionType[]>
  legalForms: () => Promise<LegalForm[]>
}

export const attributeValueService = (
  attributeValueRepository: AttributeValueRepository
): IAttributeValueService => ({
  async institutionTypes() {
    return await attributeValueRepository.institutionTypes()
  },

  async legalForms() {
    return await attributeValueRepository.legalForms()
  },
})
