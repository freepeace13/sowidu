import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import React from "react"
import { View } from "react-native"

import Style from "./SharedFilesScreenStyle"
import FileCard from "../../components/FileCard/FileCard"

function SharedFilesScreen() {
  return (
    <ScreenContainer>
      <ScreenHeader title="Shared" canGoBack onGoBack={console.log} />
      <View style={Style.content}>
        <FileCard id="1" title="asdasd" />
      </View>
    </ScreenContainer>
  )
}

export default SharedFilesScreen
