import { ReactNode, createContext, useContext, useEffect, useState } from "react"
import { Snackbar } from "react-native-paper"

interface FlashMessageContextType {
  showMessage: (message: string) => void
}

const FlashMessageContext = createContext<FlashMessageContextType>({} as FlashMessageContextType)

export const useFlashMessage = (): FlashMessageContextType => {
  const context = useContext(FlashMessageContext)
  if (!context) {
    throw new Error("useFlashMessage must be used within a FlashMessageProvider")
  }
  return context
}

interface FlashMessageProviderProps {
  children: ReactNode
}

export const FlashMessageProvider: React.FC<FlashMessageProviderProps> = ({ children }) => {
  const [flashMessageQueue, setFlashMessageQueue] = useState<string[]>([])
  const [visible, setVisible] = useState(false)
  const [currentMessage, setCurrentMessage] = useState<string | null>(null)

  const showMessage = (message: string) => {
    setFlashMessageQueue((prevQueue) => [...prevQueue, message])
  }

  useEffect(() => {
    if (flashMessageQueue.length > 0 && !visible) {
      setCurrentMessage(flashMessageQueue[0])
      setFlashMessageQueue((prevQueue) => prevQueue.splice(1))
      setVisible(true)
    }
  }, [flashMessageQueue, visible])

  const onDismiss = () => {
    setVisible(false)
  }

  return (
    <FlashMessageContext.Provider value={{ showMessage }}>
      {children}
      <Snackbar duration={Snackbar.DURATION_SHORT} visible={visible} onDismiss={onDismiss}>
        {currentMessage}
      </Snackbar>
    </FlashMessageContext.Provider>
  )
}
