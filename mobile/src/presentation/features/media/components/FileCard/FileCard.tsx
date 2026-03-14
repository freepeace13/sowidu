import { ImageURISource } from "react-native"
import { useTheme, Card, Avatar } from "react-native-paper"

import Style from "./FileCardStyle"
import { FunctionComponent } from "react"

interface FileCardProps {
  id: string
  title: string
  subtitle?: string
  icon?: (props: { size: number }) => React.ReactNode
  showCoverPhoto?: boolean
  coverPhotoUri?: ImageURISource
  highlight?: boolean
  shared?: boolean
  onPress?: (id: string) => void
}

const FileCard: FunctionComponent<FileCardProps> = ({ onPress, highlight, ...props }) => {
  const { colors } = useTheme()
  const outlineColor = !highlight ? colors.outlineVariant : colors.primary
  const renderSharedIcon = () =>
    props.shared ? (
      <Avatar.Icon
        icon="account-multiple"
        color={colors.primary}
        size={34}
        theme={{
          colors: {
            primary: colors.background,
          },
        }}
      />
    ) : undefined
  return (
    <Card
      mode="outlined"
      theme={{
        roundness: 3,
        colors: { outline: outlineColor },
      }}
      onPress={() => onPress && onPress(props.id)}
      style={Style.card}
    >
      {props.showCoverPhoto && <Card.Cover source={props.coverPhotoUri} />}
      <Card.Title
        title={props.title}
        subtitle={props.subtitle}
        style={Style.cardTitle}
        titleVariant="titleMedium"
        subtitleVariant="bodyMedium"
        right={() => renderSharedIcon()}
        rightStyle={{ marginRight: 12 }}
        left={props.icon}
      />
    </Card>
  )
}

export default FileCard
