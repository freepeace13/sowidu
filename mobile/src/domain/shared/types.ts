export type Nullable<T = unknown> = T | null | undefined

type PaginatedListMeta = {
  currentPage: number
  from: number
  lastPage: number
  perPage: number
  to: number
  total: number
}

export interface PaginatedListResponse<T> {
  data: T[]
  links: Record<string, string>
  meta: PaginatedListMeta
}

export enum Currency {
  EUR = "€",
  USD = "$",
  PHP = "₱",
  GBP = "£",
}

export type CurrencyCode = keyof typeof Currency

export enum Gender {
  Male = "male",
  Female = "female",
}

export enum AvatarImageType {
  JPG = "image/jpeg",
  PNG = "image/png",
}

export type AvatarImageSource = {
  uri: string
  name: string
  type: AvatarImageType
}
