import { BottomSheetModal } from "@gorhom/bottom-sheet"
import BottomSheetMenu from "@presentation/components/BottomSheetMenu/BottomSheetMenu"
import BottomSheetMenuItem from "@presentation/components/BottomSheetMenu/BottomSheetMenuItem"
import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { Routes } from "@presentation/routes/routes"
import { DrawerNavigationProp } from "@react-navigation/drawer"
import { ParamListBase, useNavigation } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React, { useCallback, useRef, useState } from "react"
import { View } from "react-native"
import { Appbar, Avatar, Divider, Portal, useTheme } from "react-native-paper"

import Style from "./FilesScreenStyle"
import InfiniteMediaFilesProvider from "../../contexts/InfiniteMediaFilesContext"
import FileRenamer from "../../components/FileRenamer/FileRenamer"
import FileUploadButton from "../../components/FileUploadButton/FileUploadButton"
import { useDisclose } from "@presentation/hooks/useDisclose"
import FileDetails from "../../components/FileDetails/FileDetails"
import { useProfile } from "@presentation/features/account/hooks/useProfile"
import { Media } from "@domain/media/media/Media"

function FilesScreen() {
  const { navigate } = useNavigation<StackNavigationProp<ParamListBase>>()
  const [currentItem, setCurrentItem] = useState<Media | undefined>()
  const renamer = useDisclose()
  const details = useDisclose()

  const bottomSheetRef = useRef<BottomSheetModal>(null)

  const presentMenu = useCallback((item: Media) => {
    if (bottomSheetRef.current) {
      setCurrentItem(item)
      bottomSheetRef.current.present()
    }
  }, [])

  const dismissMenu = useCallback(() => {
    if (bottomSheetRef.current) {
      bottomSheetRef.current.dismiss()
    }
  }, [])

  const actionHandler = useCallback(
    (handler: () => void) => () => {
      dismissMenu()
      requestAnimationFrame(handler)
    },
    [dismissMenu]
  )

  const handleOpen = actionHandler(() => {
    if (currentItem) {
      navigate(Routes.FileScreen as string, { id: currentItem.id })
    }
  })

  const handleDetails = actionHandler(() => {
    if (currentItem) {
      details.onPrompt()
    }
  })

  const handleShare = actionHandler(() => {
    if (currentItem) {
      navigate(Routes.ShareSettingsScreen as string, { id: currentItem.id })
    }
  })

  const handleRename = actionHandler(() => {
    if (currentItem) {
      renamer.onPrompt()
    }
  })

  return (
    <FilesScreenLayout>
      <InfiniteMediaFilesProvider
        onItemPress={presentMenu}
        isSelected={(item) => item.id === currentItem?.id}
      >
        <FileUploadButton />

        <Portal>
          <FileRenamer
            media={currentItem}
            visible={renamer.visible}
            onDismiss={renamer.onDismiss}
          />

          <FileDetails
            media={currentItem}
            visible={details.visible}
            onDismiss={details.onDismiss}
          />
        </Portal>

        {/* File Menu */}
        <BottomSheetMenu ref={bottomSheetRef}>
          <BottomSheetMenuItem
            title="Open"
            icon="eye"
            disabled={!currentItem?.permission?.canOpen}
            onPress={handleOpen}
          />
          <BottomSheetMenuItem title="Show Details" icon="information" onPress={handleDetails} />
          <Divider />
          <BottomSheetMenuItem title="Tag address" icon="map-marker" onPress={dismissMenu} />
          <BottomSheetMenuItem title="Tag category" icon="shape" onPress={dismissMenu} />
          <Divider />
          <BottomSheetMenuItem
            title="Share"
            disabled={!currentItem?.permission?.canShare}
            icon="account-multiple-plus"
            onPress={handleShare}
          />
          <BottomSheetMenuItem title="Send to" icon="send" onPress={dismissMenu} />
          <Divider />
          <BottomSheetMenuItem
            title="Rename"
            disabled={!currentItem?.permission?.canRename}
            icon="pencil"
            onPress={handleRename}
          />
          <BottomSheetMenuItem
            title="Download"
            disabled={!currentItem?.permission?.canDownload}
            icon="download"
            onPress={dismissMenu}
          />
          <Divider />
          <BottomSheetMenuItem
            title="Move to trash"
            disabled={!currentItem?.permission?.canDelete}
            icon="delete"
            onPress={dismissMenu}
          />
        </BottomSheetMenu>
      </InfiniteMediaFilesProvider>
    </FilesScreenLayout>
  )
}

type FilesScreenLayoutProps = {
  children: React.ReactNode
}

function FilesScreenLayout(props: FilesScreenLayoutProps) {
  const { colors } = useTheme()
  const profile = useProfile()
  const { openDrawer } = useNavigation<DrawerNavigationProp<ParamListBase>>()
  return (
    <ScreenContainer>
      <ScreenHeader
        title="Media"
        background={colors.background}
        left={
          <Appbar.Action
            size={32}
            icon={(iconProps) => (
              <Avatar.Image
                {...iconProps}
                source={{
                  uri: profile.avatar,
                }}
              />
            )}
            onPress={openDrawer}
          />
        }
      />
      <Divider />
      <View style={Style.content}>{props.children}</View>
    </ScreenContainer>
  )
}

export default FilesScreen
