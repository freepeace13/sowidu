import { Card, Avatar, useTheme } from "react-native-paper"
import { IconSource } from "react-native-paper/lib/typescript/components/Icon"

type CardLinkPropsType = {
  title: string
  subtitle: string
  icon: IconSource
  iconColor: string
  onPress: () => void
}

const cardTheme = (colors) => ({
  roundness: 3,
  colors: {
    outline: colors.outlineVariant,
  },
})

function CardLink(props: CardLinkPropsType) {
  const { colors } = useTheme()
  return (
    <Card mode="outlined" theme={cardTheme(colors)} onPress={props.onPress}>
      <Card.Title
        title={props.title}
        subtitle={props.subtitle}
        titleVariant="titleMedium"
        subtitleVariant="bodyMedium"
        left={(leftProps) => (
          <Avatar.Icon
            {...leftProps}
            size={40}
            icon={props.icon}
            color={colors.surface}
            style={{ backgroundColor: props.iconColor }}
          />
        )}
      />
    </Card>
  )
}

export default CardLink
