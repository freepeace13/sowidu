import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { useNavigation } from "@react-navigation/native"
import { Button, Divider, HelperText, TextInput, useTheme } from "react-native-paper"

import { View } from "react-native"
import { FunctionComponent, useState } from "react"

import { RolePicker } from "../../components"
import { useSendInvitationMutation } from "../../teamsApi"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"

import Style from "./TeamInviteMemberScreenStyle"
import {
  UsersSearchProvider,
  useUsersSearch,
} from "../../components/UsersSearch/UsersSearchProvider"
import { withoutFounder } from "@presentation/utils/permissions"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { getValidationErrorMessage } from "@application/services/helpers"

const EmailInput: FunctionComponent<{
  error?: string
  value: string | undefined
}> = ({ value, error }) => {
  const { colors } = useTheme()
  const { onPrompt } = useUsersSearch()
  return (
    <View>
      <TextInput
        mode="outlined"
        editable={false}
        label="Email"
        error={!!error}
        value={value}
        left={<TextInput.Icon icon="email" color={colors.secondary} />}
        right={<TextInput.Icon icon="account-search" color={colors.secondary} onPress={onPrompt} />}
      />
      {error && <HelperText type="error">{error}</HelperText>}
    </View>
  )
}

function TeamInviteMemberScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation()
  const { currentTeam } = useAccount()
  const [email, setEmail] = useState<string>("")
  const [role, setRole] = useState<string | string[]>("")
  const [message, setMessage] = useState<string>("")
  const flashMessage = useFlashMessage()
  const [sendInvitation, { isLoading, error }] = useSendInvitationMutation()

  const renderError = (name: string) => {
    const err = getValidationErrorMessage(error, name)
    return err && <HelperText type="error">{err}</HelperText>
  }

  const handleSubmit = async () => {
    if (!currentTeam) return
    try {
      await sendInvitation({
        teamId: currentTeam.id,
        email,
        role: role as string,
        message,
      }).unwrap()
      flashMessage.showMessage("Invitation sent")
      navigation.goBack()
    } catch (error) {
      console.log(error)
    }
  }

  if (!currentTeam) {
    return null
  }

  return (
    <UsersSearchProvider teamId={currentTeam.id} closeOnClick onPick={setEmail}>
      <ScreenContainer>
        <ScreenHeader
          title="Member Invitation"
          background={colors.background}
          onGoBack={navigation.goBack}
          canGoBack={navigation.canGoBack()}
        />
        <Divider />
        <View style={Style.content}>
          <View style={{ ...Style.form, ...Style.section }}>
            <EmailInput value={email} error={getValidationErrorMessage(error, "email")} />
            <View>
              <RolePicker
                teamId={currentTeam.id}
                title="Choose role"
                label="Role"
                value={role}
                filter={withoutFounder}
                valueExtractor={(item) => item.name}
                onValueChange={setRole}
              />
              {renderError("role")}
            </View>
            <View>
              <TextInput
                mode="outlined"
                label="Message (Optional)"
                multiline
                numberOfLines={4}
                value={message}
                onChangeText={setMessage}
              />
              {renderError("message")}
            </View>
          </View>
          <View style={Style.section}>
            <Button
              mode="contained"
              loading={isLoading}
              disabled={isLoading}
              onPress={handleSubmit}
            >
              Send Invitation
            </Button>
          </View>
        </View>
      </ScreenContainer>
    </UsersSearchProvider>
  )
}

export default TeamInviteMemberScreen
