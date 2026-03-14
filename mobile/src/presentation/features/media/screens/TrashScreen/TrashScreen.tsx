import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import React from "react"
import { View } from "react-native"

import Style from "./TrashScreenStyle"
import FileCard from "../../components/FileCard/FileCard"

function TrashScreen() {
  return (
    <ScreenContainer>
      <ScreenHeader title="Trash" canGoBack onGoBack={console.log} />
      <View style={Style.content}>
        <FileCard id="1" title="asdsad" />
      </View>
    </ScreenContainer>
  )
}

export default TrashScreen
