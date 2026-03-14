import { useIsFocused } from "@react-navigation/native"
import { View } from "react-native"
import { Appbar } from "react-native-paper"

import VideoPlayer from "./VideoPlayer"
import Style from "./style"
import { Utils, Api as MediaApi } from "../../services"

export default function WatchVideoScreen({ route, navigation }) {
  const id = route.params.mediaId
  const { data, isFetching } = MediaApi.useGetMediaDetailsQuery({ id })
  const isFocused = useIsFocused()

  if (isFetching) {
    return null
  }

  return (
    <>
      <Appbar.Header dark mode="center-aligned" statusBarHeight={0} style={Style.header}>
        <Appbar.BackAction onPress={navigation.goBack} />
        <Appbar.Content title={data?.file?.name} />
      </Appbar.Header>
      <View style={Style.container}>
        <VideoPlayer
          autoplay={isFocused}
          source={Utils.withAuthorizationHeader({ uri: data.file.uri })}
          cover={Utils.withAuthorizationHeader({ uri: data.file.thumbnail })}
          onError={(err) => {
            console.error(err)
            requestAnimationFrame(() => navigation.goBack())
          }}
          onFullscreenChange={(fullscreen) => {
            //
          }}
        />
      </View>
    </>
  )
}
