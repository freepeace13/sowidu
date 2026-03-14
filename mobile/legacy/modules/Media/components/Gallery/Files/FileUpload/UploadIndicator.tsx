import { useContext } from "react"
import { StyleSheet, View } from "react-native"
import { useTheme, Card, Avatar, ActivityIndicator } from "react-native-paper"

import { Utils as MediaUtils } from "../../../../services"
import { FilesContext } from "../Context"

interface Props {
  name?: string
  size?: number
}

function Skeleton({ name, size }: Props) {
  const { colors } = useTheme()
  return (
    <Card mode="outlined" style={Style.container} theme={{ colors: { outline: "#C0C7CD" } }}>
      <Card.Title
        title={name}
        titleVariant="titleMedium"
        titleStyle={{ color: colors.backdrop }}
        subtitle={size && MediaUtils.formatBytes(size)}
        subtitleVariant="bodyMedium"
        subtitleStyle={{ color: colors.backdrop }}
        style={{ width: "100%" }}
        left={(props) => (
          <View style={{ alignItems: "center", justifyContent: "center" }}>
            <Avatar.Icon icon="file" size={34} theme={{ colors: { primary: colors.backdrop } }} />
            <ActivityIndicator
              animating
              size={48}
              color={colors.backdrop}
              style={StyleSheet.absoluteFillObject}
            />
          </View>
        )}
      />
    </Card>
  )
}

export default function UploadingIndicator() {
  const { uploadItem } = useContext(FilesContext)
  return uploadItem ? <Skeleton name={uploadItem.name} size={uploadItem.size} /> : null
}

const Style = StyleSheet.create({
  container: {
    marginVertical: 6,
    marginHorizontal: 12,
  },
})
