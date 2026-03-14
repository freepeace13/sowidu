import { rootReducer } from "./rootReducer"
import { store, persistor } from "./configureStore"

export type AppStore = typeof store
export type RootState = ReturnType<typeof rootReducer>
export type AppDispatch = typeof store.dispatch

export { store, persistor, rootReducer }
