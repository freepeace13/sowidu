import React from "react"
import { View } from "react-native"
import { Appbar, List } from "react-native-paper"
import { Container } from "ui-module"

import ActionPicker from "./ActionPicker"
import { FilesContextProvider } from "./Context"
import FileList from "./FileList"
import UploadButton, { UploadIndicator } from "./FileUpload"
import FilterBar from "./FilterBar"
import Style from "./style"
import FileRenameContainer from "../../FileRename"

export default function FilesScreen({ navigation }) {
  return (
    <Container>
      <Appbar.Header mode="center-aligned">
        <Appbar.Action onPress={() => navigation.openDrawer()} icon="menu" />
        <Appbar.Content title="Media Library" />
      </Appbar.Header>
      <View style={Style.content}>
        <FilesContextProvider>
          <FileRenameContainer>
            <FilterBar />
            <UploadIndicator />
            <FileList />
            <UploadButton />
            <ActionPicker />
          </FileRenameContainer>
        </FilesContextProvider>
      </View>
    </Container>
  )
}
