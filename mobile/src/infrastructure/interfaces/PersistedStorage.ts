export interface PersistedStorage<T = any> {
  get: (key: string) => Promise<string | null>
  store: (key: string, value: T) => Promise<void>
  clear: (key: string) => Promise<void>
}
