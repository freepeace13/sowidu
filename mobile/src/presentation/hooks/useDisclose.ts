import { useState } from "react"

export type UseDiscloseReturn = {
  visible: boolean
  onPrompt: () => void
  onDismiss: () => void
}

export function useDisclose(): UseDiscloseReturn {
  const [visible, setVisible] = useState<boolean>(false)

  const onDismiss = () => setVisible(false)
  const onPrompt = () => setVisible(true)

  return {
    visible,
    onDismiss,
    onPrompt,
  }
}
