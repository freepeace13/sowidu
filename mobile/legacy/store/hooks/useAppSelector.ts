import { TypedUseSelectorHook, useSelector } from "react-redux"

import type { RootState } from "../configureStore"

export const useAppSelector: TypedUseSelectorHook<RootState> = useSelector
