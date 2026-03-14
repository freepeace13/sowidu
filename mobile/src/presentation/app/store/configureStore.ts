import { configureStore } from "@reduxjs/toolkit"
import { FLUSH, PAUSE, PERSIST, PURGE, REGISTER, REHYDRATE, persistStore } from "redux-persist"
import { sharedApi } from "@presentation/features/shared/api"
import { resetStateOnAccountSwitchedMiddleware } from "./middlewares/resetStateOnAccountSwitched"
import rootReducer from "./rootReducer"

const store = configureStore({
  reducer: rootReducer,
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware({
      serializableCheck: false,
    })
      .concat(sharedApi.middleware)
      .concat(resetStateOnAccountSwitchedMiddleware),
})

const persistor = persistStore(store)

export { persistor, store }
