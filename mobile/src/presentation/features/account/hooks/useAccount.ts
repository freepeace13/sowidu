import { useContext } from "react"
import { AccountContext, AccountContextType } from "../contexts/AccountContext"

export const useAccount = () => {
  const context = useContext<AccountContextType>(AccountContext)
  if (!context) {
    throw new Error("useAccount must be used within a AccountProvider")
  }
  return context
}
