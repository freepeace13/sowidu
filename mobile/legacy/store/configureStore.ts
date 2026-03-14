import { configureStore, combineReducers } from "@reduxjs/toolkit"
import { authSlice, authReducer } from "auth-module/store"
import { Api as CoreApi } from "core-module"
import { FLUSH, PAUSE, PERSIST, persistStore, PURGE, REGISTER, REHYDRATE } from "redux-persist"
import { settingsSlice, settingsReducer } from "settings-module/store"

import { refetchUserMiddleware } from "./middleware/refetchUserMiddleware"

const rootReducer = combineReducers({
  [CoreApi.reducerPath]: CoreApi.reducer,
  [authSlice.name]: authReducer,
  [settingsSlice.name]: settingsReducer,
})

export const store = configureStore({
  reducer: rootReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: {
        ignoredActions: [FLUSH, PAUSE, REHYDRATE, PERSIST, PURGE, REGISTER],
      },
    }).concat([CoreApi.middleware, refetchUserMiddleware]),
})

export const persistor = persistStore(store)

export type RootState = ReturnType<typeof store.getState>
export type AppDispatch = typeof store.dispatch
