import { FunctionComponent, useEffect, useState } from "react"
import { View } from "react-native"
import { Button, HelperText, TextInput } from "react-native-paper"
import { useUpdateTeamInfoMutation } from "../../teamsApi"
import { InstitutionPicker } from "../InstitutionPicker"
import { LegalFormPicker } from "../LegalFormPicker"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { Team } from "@domain/teams/team/Team"
import { getValidationErrors, isErrorWithMessage } from "@application/services/helpers"

interface ProfileInformationProps {
  team: Team
}

type FormState = {
  name: string
  institutionType?: number
  legalForm?: number
}

const ProfileInformation: FunctionComponent<ProfileInformationProps> = ({ team }) => {
  const [updateTeamInfo, { isLoading, error }] = useUpdateTeamInfoMutation()
  const flashMessage = useFlashMessage()
  const [teamInfo, setTeamInfo] = useState<FormState>({
    name: "",
    institutionType: undefined,
    legalForm: undefined,
  })

  useEffect(() => {
    setTeamInfo((prev) => ({
      ...prev,
      name: team.name,
      institutionType: team?.institutionType?.id,
      legalForm: team?.legalForm?.id,
    }))
  }, [team])

  const renderError = (key: keyof FormState) => {
    const errors = getValidationErrors(error, key)

    if (Array.isArray(errors) && errors.length > 0) {
      return <HelperText type="error">{errors[0]}</HelperText>
    }
  }

  const register = (name: string) => (value: any) => {
    setTeamInfo((i) => ({ ...i, [name]: value }))
  }

  const handleSubmit = async () => {
    try {
      await updateTeamInfo({
        teamId: team.id,
        name: teamInfo.name,
        legalForm: teamInfo.legalForm,
        institutionType: teamInfo.institutionType,
      }).unwrap()

      flashMessage.showMessage("Saved")
    } catch (e) {
      if (isErrorWithMessage(e)) {
        flashMessage.showMessage(e.message)
      }
    }
  }

  return (
    <>
      <View>
        <TextInput
          mode="outlined"
          label="Name"
          disabled={isLoading}
          value={teamInfo.name}
          onChangeText={register("name")}
        />
        {renderError("name")}
      </View>
      <View>
        <LegalFormPicker
          title="Legal Form"
          disabled={isLoading}
          value={teamInfo.legalForm}
          onValueChange={register("legalForm")}
        />
        {renderError("legalForm")}
      </View>
      <View>
        <InstitutionPicker
          title="Institution Type"
          placeholder="Choose"
          disabled={isLoading}
          value={teamInfo.institutionType}
          onValueChange={register("institutionType")}
        />
        {renderError("institutionType")}
      </View>
      <Button mode="contained" onPress={handleSubmit} loading={isLoading}>
        Save
      </Button>
    </>
  )
}

export default ProfileInformation
