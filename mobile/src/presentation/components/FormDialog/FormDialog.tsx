import { Button, Dialog, useTheme } from "react-native-paper"
import Style from "./FormDialogStyle"
import { UseDiscloseReturn } from "@presentation/hooks/useDisclose"
import { PropsWithChildren } from "react"

type FormDialogProps = Omit<UseDiscloseReturn, "onPrompt"> &
  PropsWithChildren<{
    title: string
    isLoading?: boolean
    submitButtonText?: string
    onSubmit: () => void
  }>
function FormDialog(props: FormDialogProps) {
  const {
    title,
    children,
    visible,
    onDismiss,
    isLoading,
    submitButtonText = "Submit",
    onSubmit,
  } = props
  const { colors } = useTheme()
  return (
    <Dialog
      visible={visible}
      onDismiss={onDismiss}
      theme={{ roundness: 1 }}
      style={{ backgroundColor: colors.background }}
    >
      <Dialog.Title>{title}</Dialog.Title>
      <Dialog.Content>{children}</Dialog.Content>
      <Dialog.Actions>
        <Button mode="text" onPress={onDismiss} contentStyle={Style.actions} disabled={isLoading}>
          Cancel
        </Button>
        <Button
          mode="contained"
          onPress={onSubmit}
          contentStyle={Style.actions}
          disabled={isLoading}
          loading={isLoading}
        >
          {submitButtonText}
        </Button>
      </Dialog.Actions>
    </Dialog>
  )
}

export default FormDialog
