import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { NavigationProp, ParamListBase, useNavigation } from "@react-navigation/native"
import { FunctionComponent } from "react"
import { View } from "react-native"
import { useTheme } from "react-native-paper"

import { UserProfileForm } from "@presentation/features/user/components/ProfileForm"
import { TeamProfileForm } from "@presentation/features/teams/components/ProfileForm"

import Style from "./ProfileScreenStyle"
import { useAccount } from "../../hooks/useAccount"

interface ProfileScreenProps {}

const ProfileScreen: FunctionComponent<ProfileScreenProps> = () => {
  const { colors } = useTheme()
  const navigation = useNavigation<NavigationProp<ParamListBase>>()
  const { user } = useAccount()

  if (!user) {
    return null
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="Edit Profile"
        background={colors.background}
        onGoBack={() => navigation.goBack()}
        canGoBack={navigation.canGoBack()}
      />
      <View style={Style.content}>
        {!user?.currentTeam ? <UserProfileForm /> : <TeamProfileForm />}
      </View>
    </ScreenContainer>
  )
}

export default ProfileScreen
