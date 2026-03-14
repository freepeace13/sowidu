import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import Notifications from "@presentation/features/notification/components/Notifications"
import { useNavigation } from "@react-navigation/native"
import { View } from "react-native"

import Style from "./NotificationScreenStyle"

function NotificationScreen() {
  const navigation = useNavigation()
  return (
    <ScreenContainer>
      <ScreenHeader
        title="Notification"
        canGoBack={navigation.canGoBack()}
        onGoBack={navigation.goBack}
      />
      <View style={Style.content}>
        <Notifications />
      </View>
    </ScreenContainer>
  )
}

export default NotificationScreen
