import { FunctionComponent } from "react"
import { View } from "react-native"
import { Divider, Icon, Text, useTheme } from "react-native-paper"

import Style from "./SectionGroupStyle"

interface SectionGroupProps {
  icon?: string
  title?: string
  children: React.ReactNode
  outlined?: boolean
}

const SectionGroup: FunctionComponent<SectionGroupProps> = ({
  title,
  icon,
  children,
  outlined = true,
}) => {
  const theme = useTheme()
  return (
    <View style={Style.container}>
      {title && (
        <View
          style={{
            flexDirection: "row",
            alignItems: "center",
            gap: 8,
            marginLeft: 10,
          }}
        >
          {icon && <Icon source={icon} size={24} color={theme.colors.primary} />}
          <Text variant="titleMedium">{title}</Text>
        </View>
      )}
      {outlined ? <Divider style={Style.divider} /> : <View style={{ height: 12 }} />}
      <View style={Style.content}>{children}</View>
    </View>
  )
}

export default SectionGroup
