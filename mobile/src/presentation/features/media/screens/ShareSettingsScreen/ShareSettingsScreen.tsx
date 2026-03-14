import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { ParamListBase, useNavigation, useRoute } from "@react-navigation/native"
import { StackNavigationProp } from "@react-navigation/stack"
import React from "react"
import { View } from "react-native"
import { IconButton, useTheme } from "react-native-paper"

import Style from "./ShareSettingsScreenStyle"
import SharedPeople from "../../components/SharedPeople/SharedPeople"
import SearchUnsharedUsers from "../../components/SearchUnsharedUsers/SearchUnsharedUsers"
import { useShareToMutation, useUnshareFromMutation } from "../../mediaApi"
import { MediaReadWritePermission } from "@domain/media/types"
import { MediaUser } from "@domain/media/shares/MediaUser"

function ShareSettingsScreen() {
  const { colors } = useTheme()
  const route: any = useRoute()
  const navigation = useNavigation<StackNavigationProp<ParamListBase>>()

  const [unshareFrom] = useUnshareFromMutation()
  const [shareTo] = useShareToMutation()

  const mediaId = route.params.id

  const handleShareToUser = async (user: any) => {
    shareTo({
      mediaId,
      urn: user.urn,
      scope: MediaReadWritePermission.Read,
    })
  }

  const handleUnshareFromUser = async (user: MediaUser) => {
    unshareFrom({
      mediaId,
      urn: user.urn,
    })
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Share with people"
        background={colors.background}
        left={<IconButton icon="close" onPress={navigation.goBack} />}
        canGoBack={false}
      />
      <View style={Style.content}>
        <SearchUnsharedUsers mediaId={mediaId} onPress={handleShareToUser} limit={3} />
        <SharedPeople mediaId={mediaId} onRemove={handleUnshareFromUser} />
      </View>
    </ScreenContainer>
  )
}

export default ShareSettingsScreen
