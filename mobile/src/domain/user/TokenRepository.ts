export interface TokenRepository {
  current: () => Promise<string | null>
  store: (token: string) => Promise<void>
  delete: () => Promise<void>
}
