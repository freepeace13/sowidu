import { ReactNativeZoomableView } from "@openspacelabs/react-native-zoomable-view"
import { Image } from "expo-image"
import { View, Dimensions } from "react-native"
import { Appbar } from "react-native-paper"

import Style from "./style"
import { Utils as MediaUtils, Api as MediaApi } from "../../services"

const WindowSize = Dimensions.get("window")

export default function ImagePreviewScreen({ route, navigation }) {
  const id = route.params.mediaId
  const { data, isFetching } = MediaApi.useGetMediaDetailsQuery({ id })

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
        <ReactNativeZoomableView
          zoomEnabled
          initialZoom={1}
          minZoom={1}
          maxZoom={5}
          zoomStep={5}
          bindToBorders
          movementSensibility={0.8}
          panBoundaryPadding={0}
          disablePanOnInitialZoom
          contentWidth={WindowSize.width}
          contentHeight={(WindowSize.width + WindowSize.height) / 2 - WindowSize.width}
        >
          <Image
            source={MediaUtils.transformToPrivateSource(data?.file?.responsive)}
            resizeMode="center"
            style={Style.image}
          />
        </ReactNativeZoomableView>
      </View>
    </>
  )
}
