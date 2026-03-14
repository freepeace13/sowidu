import { PersistedStorage } from "@infrastructure/interfaces/PersistedStorage"
import AsyncStore from "@react-native-async-storage/async-storage"

export const asyncPersistedStorage: PersistedStorage = {
  get: async (key) => {
    return await AsyncStore.getItem(key)
  },

  store: async (key: string, value: string) => {
    await AsyncStore.setItem(key, value)
  },

  clear: async (key: string) => {
    await AsyncStore.removeItem(key)
  },
}
