import React from "react"
import { View } from "react-native"
import { Appbar } from "react-native-paper"
import Pdf from "react-native-pdf"

import Style from "./style"
import { Utils as MediaUtils, Api as MediaApi } from "../../services"

export default function ReadDocumentScreen({ route, navigation }) {
  const id = route.params.mediaId
  const { data, isFetching } = MediaApi.useGetMediaDetailsQuery({ id })

  const onLoad = () => {
    // handle on load
  }

  const onPageChanged = () => {
    // handle page change
  }

  const onLinkPress = () => {
    // handle link press
  }

  const onError = () => {
    // handle error
  }

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
        <Pdf
          source={{
            ...MediaUtils.withAuthorizationHeader({ uri: data.file.uri }),
            cache: true,
          }}
          trustAllCerts={false}
          onLoadComplete={onLoad}
          onPageChanged={onPageChanged}
          onError={onError}
          onPressLink={onLinkPress}
          style={Style.pdf}
        />
      </View>
    </>
  )
}
