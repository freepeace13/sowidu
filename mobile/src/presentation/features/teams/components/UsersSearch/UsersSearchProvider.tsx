import { useDisclose } from "@presentation/hooks/useDisclose"
import { ReactNode, createContext, useContext } from "react"
import UsersSearchModal from "./UsersSearchModal"

type UsersSearchContextType = {
  onPrompt: () => void
}

const UsersSearchContext = createContext<UsersSearchContextType>({} as UsersSearchContextType)

export const useUsersSearch = (): UsersSearchContextType => {
  const context = useContext(UsersSearchContext)
  if (!context) {
    throw new Error("useUsersSearch must be used within a UsersSearchProvider")
  }
  return context
}

interface UsersSearchProviderProps {
  teamId: number
  onPick: (email: string) => void
  closeOnClick?: boolean
  children: ReactNode
}

export const UsersSearchProvider: React.FC<UsersSearchProviderProps> = ({
  teamId,
  closeOnClick,
  onPick,
  children,
}) => {
  const { visible, onDismiss, onPrompt } = useDisclose()

  const handleItemPress = (email: string) => {
    onPick(email)
    if (closeOnClick) {
      onDismiss()
    }
  }

  return (
    <UsersSearchContext.Provider value={{ onPrompt }}>
      {children}
      <UsersSearchModal
        title="Find People"
        teamId={teamId}
        visible={visible}
        onDismiss={onDismiss}
        onItemPress={handleItemPress}
      />
    </UsersSearchContext.Provider>
  )
}
