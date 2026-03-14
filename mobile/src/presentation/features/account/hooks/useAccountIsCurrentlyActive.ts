import { useAccount } from "../hooks/useAccount"

type AccountType = "organization" | "personal"

export function useAccountIsCurrentlyActive(account: {
  id: number
  name: string
  type: AccountType
  photoUri: string
}): boolean {
  const { user, currentTeam } = useAccount()

  if (user) {
    switch (account.type) {
      case "personal":
        return !currentTeam?.id && user.id === account.id
      case "organization":
        return account.id === currentTeam?.id
      default:
        return false
    }
  }

  return false
}
