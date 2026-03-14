import { FormDialog } from "@presentation/components"
import { useState } from "react"
import { Keyboard, View } from "react-native"
import { HelperText, TextInput } from "react-native-paper"
import { useCreateRoleMutation } from "../../teamsApi"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { getValidationErrorMessage } from "@application/services/helpers"

export const CreateRoleFormDialog: React.FC<{
  teamId: number
  visible: boolean
  onDismiss: () => void
}> = ({ visible, teamId, onDismiss }) => {
  const flashMessage = useFlashMessage()
  const [roleName, setRoleName] = useState<string>("")
  const [createRoleAsync, { isLoading, error }] = useCreateRoleMutation()

  const renderError = (name: string) => {
    const err = getValidationErrorMessage(error, name)
    return err && <HelperText type="error">{err}</HelperText>
  }

  const handleSubmit = async () => {
    try {
      await createRoleAsync({ teamId, roleName }).unwrap()
      setRoleName("")
      onDismiss()
      flashMessage.showMessage("Role successfully created")
    } catch {
      //
    }
  }

  return (
    <FormDialog
      title="Create Role"
      submitButtonText="Create"
      visible={visible}
      isLoading={isLoading}
      onDismiss={onDismiss}
      onSubmit={handleSubmit}
    >
      <View>
        <TextInput
          dense
          mode="outlined"
          onSubmitEditing={Keyboard.dismiss}
          value={roleName}
          disabled={isLoading}
          onChangeText={setRoleName}
          error={!!getValidationErrorMessage(error, "name")}
        />
        {renderError("name")}
      </View>
    </FormDialog>
  )
}
