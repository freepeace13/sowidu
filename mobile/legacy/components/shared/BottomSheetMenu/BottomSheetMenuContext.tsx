import { createContext, useContext } from "react"

export const BottomSheetMenuContext = createContext({
  opened: false,
  close: () => {},
  options: {
    height: null,
    closeOnClick: true,
  },
})

export const useBottomSheetMenuContext = () => useContext(BottomSheetMenuContext)
