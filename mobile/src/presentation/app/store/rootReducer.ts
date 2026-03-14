import {
  preferenceSlice,
  preferenceReducer,
} from "@presentation/features/preference/preferenceSlice"
import { teamsSlice, teamsReducer } from "@presentation/features/teams/teamsSlice"
import { combineReducers } from "@reduxjs/toolkit"
import { sharedApi } from "@presentation/features/shared/api"
import { accountReducer, accountSlice } from "@presentation/features/account/accountSlice"
import { persistReducer } from "redux-persist"
import { APP_DEBUG } from "@infrastructure/config"
import AsyncStorage from "@react-native-async-storage/async-storage"

const rootReducer = combineReducers({
  [sharedApi.reducerPath]: sharedApi.reducer,
  [accountSlice.name]: accountReducer,
  [teamsSlice.name]: teamsReducer,
  [preferenceSlice.name]: preferenceReducer,
})

export default persistReducer(
  {
    key: "root",
    debug: APP_DEBUG,
    storage: AsyncStorage,
    whitelist: ["account"],
  },
  rootReducer
)
