import { ReactNativeZoomableView } from "@openspacelabs/react-native-zoomable-view"
import { View, Dimensions, Image, ImageURISource } from "react-native"

import Style from "./ZoomableImageStyle"
import { useState } from "react"
import { ActivityIndicator, useTheme } from "react-native-paper"

const WINDOW_SIZE = Dimensions.get("window")

interface Props {
  source: ImageURISource | ImageURISource[]
}

function ZoomableImage(props: Props) {
  const [isLoading, setIsLoading] = useState(true)
  const { colors } = useTheme()
  return (
    <View style={Style.container}>
      <ReactNativeZoomableView
        zoomEnabled
        initialZoom={1}
        minZoom={1}
        maxZoom={4}
        zoomStep={4}
        bindToBorders
        movementSensibility={1}
        panBoundaryPadding={0}
        disablePanOnInitialZoom
        contentWidth={WINDOW_SIZE.width}
        contentHeight={120}
      >
        {isLoading && (
          <ActivityIndicator
            size="large"
            style={Style.loadingIndicator}
            color={colors.background}
          />
        )}
        <Image
          source={props.source}
          resizeMode="contain"
          onLoad={() => setIsLoading(false)}
          fadeDuration={100}
          style={Style.image}
        />
      </ReactNativeZoomableView>
    </View>
  )
}

export default ZoomableImage
