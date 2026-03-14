import { progressPercentage } from "@presentation/utils/math"
import { View } from "react-native"
import { useTheme, Card, Avatar, ProgressBar } from "react-native-paper"

import Style from "./UploadProgressStyle"
import { UploadEntity } from "../../reducer"
import { FunctionComponent } from "react"

type UploadProgressProps = {
  item: UploadEntity
}

const UploadProgress: FunctionComponent<UploadProgressProps> = ({ item }) => {
  const { colors } = useTheme()
  return (
    <Card
      mode="outlined"
      theme={{ roundness: 3, colors: { outline: colors.outlineVariant } }}
      onPress={() => {}}
      style={Style.card}
    >
      <View style={Style.container}>
        <View style={Style.left}>
          <Avatar.Icon
            size={40}
            icon="file"
            theme={{
              colors: {
                primary: colors.outline,
              },
            }}
          />
        </View>
        <View style={Style.titles}>
          <View style={Style.progress}>
            {item.progress && (
              <ProgressBar
                color={colors.outline}
                progress={progressPercentage(
                  item.progress.totalBytesSent,
                  item.progress.totalBytesExpectedToSend
                )}
              />
            )}
          </View>
        </View>
        <View />
      </View>
    </Card>
  )
}

export default UploadProgress
