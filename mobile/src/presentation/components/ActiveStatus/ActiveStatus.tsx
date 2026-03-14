import { FunctionComponent } from "react"
import { Text } from "react-native-paper"

interface ActiveStatusProps {
  value: Date | string
}

const ActiveStatus: FunctionComponent<ActiveStatusProps> = ({ value }) => {
  const now = Date.now()
  const lastActive = (typeof value === "string" ? new Date(value) : value).getTime()
  const minsElapsed = Math.floor((now - lastActive) / 1000 / 60) % 60
  return (
    <Text variant="labelLarge" style={{ color: "green" }}>
      Active {minsElapsed} ago
    </Text>
  )
}

export default ActiveStatus
