import { FunctionComponent } from "react"
import { View } from "react-native"
import { Divider } from "react-native-paper"
import { KeyboardAwareScrollView, KeyboardToolbar } from "react-native-keyboard-controller"

import Style from "./ProfileFormStyle"
import ProfileAvatar from "./ProfileAvatar"
import ProfileInformation from "./ProfileInformation"
import { useAccount } from "@presentation/features/account/hooks/useAccount"

interface TeamProfileFormProps {}

const TeamProfileForm: FunctionComponent<TeamProfileFormProps> = () => {
  const { currentTeam } = useAccount()
  return currentTeam ? (
    <>
      <KeyboardAwareScrollView style={Style.keyboardAwareScrollView} bottomOffset={72}>
        <ProfileAvatar team={currentTeam} />
        <Divider style={Style.divider} />
        <View style={Style.stack}>
          <ProfileInformation team={currentTeam} />
        </View>
      </KeyboardAwareScrollView>
      <KeyboardToolbar />
    </>
  ) : null
}

export default TeamProfileForm
