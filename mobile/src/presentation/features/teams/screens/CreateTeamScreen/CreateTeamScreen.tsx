import ScreenHeader from "@presentation/components/Header/ScreenHeader/ScreenHeader"
import ScreenContainer from "@presentation/components/ScreenContainer/ScreenContainer"
import { useNavigation } from "@react-navigation/native"
import { View } from "react-native"
import { Divider, useTheme } from "react-native-paper"

import Style from "./CreateTeamScreenStyle"
import { useCreateTeamMutation } from "../../teamsApi"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import CreateTeamForm from "../../components/CreateTeamForm/CreateTeamForm"

function CreateTeamScreen() {
  const { colors } = useTheme()
  const navigation = useNavigation()
  const [createTeam, { isLoading }] = useCreateTeamMutation()
  const flashMessage = useFlashMessage()

  const handleSubmit = async (formData: any) => {
    await createTeam({
      name: formData.name,
      institutionTypeId: formData.institutionTypeId,
      legalFormId: formData.legalFormId,
    }).unwrap()
    flashMessage.showMessage("Organization created successfully!")
  }

  return (
    <ScreenContainer>
      <ScreenHeader
        title="New Organization"
        canGoBack={navigation.canGoBack()}
        onGoBack={navigation.goBack}
        background={colors.background}
      />
      <Divider />
      <View style={Style.content}>
        <CreateTeamForm isLoading={isLoading} onCreate={handleSubmit} />
      </View>
    </ScreenContainer>
  )
}

export default CreateTeamScreen
