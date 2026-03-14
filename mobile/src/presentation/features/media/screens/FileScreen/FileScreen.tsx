import PdfReader from "@presentation/components/PdfReader/PdfReader"
import VideoPlayer from "@presentation/components/VideoPlayer/VideoPlayer"
import ZoomableImage from "@presentation/components/ZoomableImage/ZoomableImage"
import { useIsFocused, useRoute } from "@react-navigation/native"
import * as Sentry from "@sentry/react-native"
import { AVPlaybackSource } from "expo-av"
import { ImageURISource } from "react-native"
import { Appbar, Text } from "react-native-paper"
import { Source as PdfSource } from "react-native-pdf"

import Style from "./FileScreenStyle"
import { usePrivateURiSource } from "../../hooks/usePrivateURiSource"
import { useGetFileInfoQuery } from "../../mediaApi"
import BasicLayout from "@presentation/components/ScreenLayout/BasicLayout"
import { useMemo } from "react"

function FileScreen() {
  const route = useRoute()
  const isFocused = useIsFocused()
  const privateUriSource = usePrivateURiSource()
  const { data } = useGetFileInfoQuery({ mediaId: (route?.params as any).id as string })

  const title = useMemo(() => (route?.params as any)?.title || data?.title || "", [route, data])

  return (
    <BasicLayout title={title} right={<Appbar.Action icon="information" onPress={console.log} />}>
      {data &&
        (data.file.type === "image" ? (
          <ZoomableImage
            source={(data.file.responsive as any).map(privateUriSource) as ImageURISource[]}
          />
        ) : data.file.type === "video" ? (
          <VideoPlayer
            autoplay={isFocused}
            source={privateUriSource(data.file.source) as AVPlaybackSource}
            cover={privateUriSource(data.file.thumbnail)}
            onError={Sentry.captureException}
            onFullscreenChange={(fullscreen) => {
              console.log("isFullscreenMode", fullscreen)
            }}
          />
        ) : data.file.type === "pdf" ? (
          <PdfReader
            source={privateUriSource(data.file.source) as PdfSource}
            onError={Sentry.captureException}
          />
        ) : (
          <Text style={Style.text}>Not Supported</Text>
        ))}
    </BasicLayout>
  )
}

export default FileScreen
