import { StyleProp, ViewStyle } from "react-native"
import { Card, Icon, IconButton, useTheme } from "react-native-paper"
import { ThemeProp } from "react-native-paper/lib/typescript/types"

interface Props {
  title: string
  icon: any
  subtitle?: string
  onPress?: () => void
}

function LinkCard({ onPress, title, subtitle, icon }: Props) {
  const { colors } = useTheme()
  const cardTheme: ThemeProp = { roundness: 0, colors: { outline: colors.outlineVariant } }
  const cardStyle: StyleProp<ViewStyle> = { backgroundColor: colors.background }
  return (
    <Card mode="contained" style={cardStyle} theme={cardTheme} onPress={onPress}>
      <Card.Title
        title={title}
        titleVariant="titleMedium"
        subtitle={subtitle}
        subtitleVariant="bodyMedium"
        subtitleStyle={{ color: colors.outline }}
        left={(leftProps) => <Icon {...leftProps} source={icon} color={colors.secondary} />}
        right={(rightProps) => (
          <IconButton {...rightProps} icon="chevron-right" iconColor={colors.secondary} />
        )}
      />
    </Card>
  )
}

export default LinkCard
