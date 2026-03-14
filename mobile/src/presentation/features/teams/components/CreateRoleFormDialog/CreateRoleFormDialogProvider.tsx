import { useDisclose } from "@presentation/hooks/useDisclose"
import { ReactNode, createContext, useContext } from "react"
import { CreateRoleFormDialog } from "./CreateRoleFormDialog"
import { Portal } from "react-native-paper"

type CreateRoleFormDialogContextType = {
  onPrompt: () => void
}

const CreateRoleFormDialogContext = createContext<CreateRoleFormDialogContextType>(
  {} as CreateRoleFormDialogContextType
)

export const useCreateRoleFormDialog = (): CreateRoleFormDialogContextType => {
  const context = useContext(CreateRoleFormDialogContext)
  if (!context) {
    throw new Error("useCreateRoleFormDialog must be used within a CreateRoleFormDialogProvider")
  }
  return context
}

interface CreateRoleFormDialogProviderProps {
  teamId: number
  children: ReactNode
}

export const CreateRoleFormDialogProvider: React.FC<CreateRoleFormDialogProviderProps> = ({
  teamId,
  children,
}) => {
  const { visible, onPrompt, onDismiss } = useDisclose()
  return (
    <CreateRoleFormDialogContext.Provider value={{ onPrompt }}>
      {children}
      <Portal>
        <CreateRoleFormDialog teamId={teamId} visible={visible} onDismiss={onDismiss} />
      </Portal>
    </CreateRoleFormDialogContext.Provider>
  )
}
