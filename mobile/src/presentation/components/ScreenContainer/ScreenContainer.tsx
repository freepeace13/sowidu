import { PropsWithChildren } from "react"
import { View } from "react-native"
import Style from "./ScreenContainerStyle"
import { useTheme } from "react-native-paper"

type ScreenContainerPropsType = PropsWithChildren & {
  background?: string
  safeEdges?: {
    top?: number
    bottom?: number
    right?: number
    left?: number
  }
}

function ScreenContainer(props: ScreenContainerPropsType) {
  const { colors } = useTheme()
  const { background = colors.background, safeEdges } = props
  return (
    <View style={[Style.container, background && { backgroundColor: background }, safeEdges]}>
      {props.children}
    </View>
  )
}

export default ScreenContainer
