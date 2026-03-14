import { GenderPicker } from "@presentation/components/GenderPicker"
import { FunctionComponent, useEffect, useState } from "react"
import { View } from "react-native"
import { Button, HelperText, TextInput } from "react-native-paper"
import { DateTimePickerEvent, DateTimePickerAndroid } from "@react-native-community/datetimepicker"
import { useUpdateUserInfoMutation } from "../../userApi"
import { Nullable } from "@domain/shared/types"
import { useFlashMessage } from "@presentation/components/FlashMessage/FlashMessageProvider"
import { useAccount } from "@presentation/features/account/hooks/useAccount"
import { getValidationErrors, isErrorWithMessage } from "@application/services/helpers"

interface ProfileInformationProps {}

type FormState = {
  firstName: string
  lastName: string
  birthdate: Nullable<Date>
  gender: Nullable<string>
}

const ProfileInformation: FunctionComponent<ProfileInformationProps> = () => {
  const { user } = useAccount()
  const flashMessage = useFlashMessage()
  const [updateInfo, { isLoading, error }] = useUpdateUserInfoMutation()
  const [userInfo, setUserInfo] = useState<FormState>({
    firstName: "",
    lastName: "",
    birthdate: null,
    gender: null,
  })

  useEffect(() => {
    if (user) {
      const { birthdate, gender, ...extra } = user
      setUserInfo({
        ...extra,
        birthdate: birthdate ? new Date(birthdate) : null,
        gender: gender || null,
      })
    }
  }, [user])

  const renderError = (key: keyof FormState) => {
    const errors = getValidationErrors(error, key)

    if (Array.isArray(errors) && errors.length > 0) {
      return <HelperText type="error">{errors[0]}</HelperText>
    }
  }

  const register = (name: string) => (value: any) => {
    setUserInfo((i) => ({ ...i, [name]: value }))
  }

  const onChange = (event: DateTimePickerEvent): void => {
    const currentDate = new Date(event.nativeEvent.timestamp)
    setUserInfo((i) => ({ ...i, birthdate: currentDate }))
  }

  const selectBirthdate = () => {
    if (userInfo.birthdate) {
      DateTimePickerAndroid.open({
        value: userInfo.birthdate,
        onChange,
        mode: "date",
        is24Hour: false,
      })
    }
  }

  const handleSubmit = async () => {
    try {
      await updateInfo({
        firstName: userInfo.firstName,
        lastName: userInfo.lastName,
        birthdate: userInfo.birthdate,
        gender: userInfo.gender,
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
          label="First Name"
          disabled={isLoading}
          value={userInfo.firstName}
          onChangeText={register("firstName")}
        />
        {renderError("firstName")}
      </View>
      <View>
        <TextInput
          mode="outlined"
          label="Last Name"
          disabled={isLoading}
          value={userInfo.lastName}
          onChangeText={register("lastName")}
        />
        {renderError("lastName")}
      </View>
      <View>
        <TextInput
          mode="outlined"
          label="Birthdate"
          placeholder="mm/dd/yyyy"
          editable={false}
          disabled={isLoading}
          value={userInfo.birthdate?.toLocaleDateString()}
          right={
            <TextInput.Icon
              icon="calendar-month"
              onPress={selectBirthdate}
              forceTextInputFocus={false}
            />
          }
        />
        {renderError("birthdate")}
      </View>
      <View>
        <GenderPicker
          title="Gender"
          disabled={isLoading}
          value={userInfo.gender}
          onValueChange={register("gender")}
        />
        {renderError("gender")}
      </View>
      <Button onPress={handleSubmit} loading={isLoading}>
        Save
      </Button>
    </>
  )
}

export default ProfileInformation
