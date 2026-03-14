import { useNavigation } from "@react-navigation/native"
import { FunctionComponent, ReactNode } from "react"
import { useTheme } from "react-native-paper"
import ScreenContainer from "../ScreenContainer/ScreenContainer"
import ScreenHeader from "../Header/ScreenHeader/ScreenHeader"
import { StyleSheet, View } from "react-native"

interface BasicLayoutProps {
  right?: React.JSX.Element
  title?: string
  children: React.ReactNode
}

const BasicLayout: FunctionComponent<BasicLayoutProps> = ({ children, ...props }) => {
  const { colors } = useTheme()
  const navigation = useNavigation()
  return (
    <ScreenContainer>
      <ScreenHeader
        {...props}
        title={props.title || ""}
        background={colors.background}
        onGoBack={navigation.goBack}
        canGoBack={navigation.canGoBack()}
      />
      <View style={Style.content}>{children}</View>
    </ScreenContainer>
  )
}

const Style = StyleSheet.create({
  content: {
    flex: 1,
  },
})

export default BasicLayout
