import { useAccount } from "../hooks/useAccount"

interface Profile {
  name: string | undefined
  avatar: string | undefined
}

export const useProfile = (): Profile => {
  const { user, currentTeam } = useAccount()
  return {
    name: currentTeam?.name ?? user?.fullName,
    avatar: currentTeam?.photoURL ?? user?.photoURL,
  }
}
