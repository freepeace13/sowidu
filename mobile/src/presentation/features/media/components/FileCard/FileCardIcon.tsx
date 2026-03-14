import { FunctionComponent } from "react"
import { Avatar } from "react-native-paper"
import * as Constants from "@presentation/features/media/constants"
import { MediaType } from "@domain/media/types"

interface FileCardIconProps {
  size: number
  type: MediaType
}

const FileCardIcon: FunctionComponent<FileCardIconProps> = ({ type, size }) => {
  return (
    <Avatar.Icon
      size={size}
      icon={Constants.icons[type]}
      theme={{
        colors: {
          onPrimary: "white",
          primary: Constants.colors[type],
        },
      }}
    />
  )
}

export default FileCardIcon
