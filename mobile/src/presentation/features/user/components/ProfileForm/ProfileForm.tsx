import { FunctionComponent } from "react"
import { View } from "react-native"
import { Divider } from "react-native-paper"
import { KeyboardAwareScrollView, KeyboardToolbar } from "react-native-keyboard-controller"

import Style from "./ProfileFormStyle"
import ProfileAvatar from "./ProfileAvatar"
import ProfileInformation from "./ProfileInformation"

interface UserProfileFormProps {}

const UserProfileForm: FunctionComponent<UserProfileFormProps> = () => {
  return (
    <>
      <KeyboardAwareScrollView style={Style.keyboardAwareScrollView} bottomOffset={72}>
        <ProfileAvatar />
        <Divider style={Style.divider} />
        <View style={Style.stack}>
          <ProfileInformation />
        </View>
      </KeyboardAwareScrollView>
      <KeyboardToolbar />
    </>
  )
}

export default UserProfileForm
