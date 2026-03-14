import { InstitutionType, LegalForm } from "./Team"

export interface AttributeValueRepository {
  institutionTypes: () => Promise<InstitutionType[]>
  legalForms: () => Promise<LegalForm[]>
}
