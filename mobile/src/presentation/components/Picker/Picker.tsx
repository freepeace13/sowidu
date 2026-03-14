import { useState } from "react"
import { Button, Dialog, Portal, useTheme } from "react-native-paper"

import Style from "./PickerStyle"

interface Props extends React.PropsWithChildren {
  title: string
  onDismissed?: () => void
  anchor: (context: { showDialog: () => void; visible: boolean }) => React.ReactNode
}

function Picker({ title, anchor, onDismissed, children }: Props) {
  const { colors, fonts } = useTheme()
  const [dialogVisible, setDialogVisible] = useState(false)

  const hideDialog = () => {
    setDialogVisible(false)

    if (typeof onDismissed === "function") {
      onDismissed()
    }
  }

  const showDialog = () => setDialogVisible(true)

  return (
    <>
      {anchor && anchor({ showDialog, visible: dialogVisible })}
      <Portal>
        <Dialog
          visible={dialogVisible}
          onDismiss={hideDialog}
          theme={{ roundness: 1 }}
          style={{ backgroundColor: colors.background }}
        >
          {title && <Dialog.Title style={{ ...fonts.titleLarge }}>{title}</Dialog.Title>}
          <Dialog.Content>{children}</Dialog.Content>
          <Dialog.Actions>
            <Button onPress={hideDialog} contentStyle={Style.actionButton} compact>
              Done
            </Button>
          </Dialog.Actions>
        </Dialog>
      </Portal>
    </>
  )
}

export default Picker
