import EmailInput from "@presentation/components/EmailInput/EmailInput"
import PasswordInput from "@presentation/components/PasswordInput/PasswordInput"
import Stack from "@presentation/components/Stack/Stack"
import { TextInput, Text, Checkbox, Button, useTheme, HelperText } from "react-native-paper"

import Style from "./RegisterFormStyle"
import { CustomForm, CustomFormField } from "@presentation/components/CustomForm"
import { useRef } from "react"
import { FormRef } from "@presentation/components/CustomForm/CustomForm"

type RegisterFormPropsType = {
  title: string
  isLoading: boolean
  onRegister: (values: {
    firstName: string
    lastName: string
    email: string
    password: string
  }) => Promise<void>
}

function RegisterForm(props: RegisterFormPropsType) {
  const { colors } = useTheme()
  const formRef = useRef<FormRef>(null)
  return (
    <Stack direction="column" space={0}>
      <Text variant="bodyMedium" style={Style.title}>
        {props.title}
      </Text>
      <CustomForm ref={formRef} onSubmit={props.onRegister}>
        <Stack direction="column" style={Style.formStack}>
          <CustomFormField
            name="firstName"
            render={({ value, onChange, error }) => (
              <>
                <TextInput
                  label="First Name"
                  mode="outlined"
                  disabled={props.isLoading}
                  activeOutlineColor={colors.primary}
                  outlineColor="#71787D"
                  textColor="#40484C"
                  value={value}
                  onChangeText={onChange}
                />
                {error && <HelperText type="error">{error}</HelperText>}
              </>
            )}
          />
          <CustomFormField
            name="lastName"
            render={({ value, onChange, error }) => (
              <>
                <TextInput
                  label="Last Name"
                  mode="outlined"
                  disabled={props.isLoading}
                  activeOutlineColor={colors.primary}
                  outlineColor="#71787D"
                  textColor="#40484C"
                  value={value}
                  onChangeText={onChange}
                />
                {error && <HelperText type="error">{error}</HelperText>}
              </>
            )}
          />
          <CustomFormField
            name="email"
            render={({ value, onChange, error }) => (
              <>
                <EmailInput
                  label="Email"
                  disabled={props.isLoading}
                  activeOutlineColor={colors.primary}
                  value={value}
                  onChangeText={onChange}
                />
                {error && <HelperText type="error">{error}</HelperText>}
              </>
            )}
          />
          <CustomFormField
            name="password"
            render={({ value, onChange, error }) => (
              <>
                <PasswordInput
                  label="Password"
                  disabled={props.isLoading}
                  value={value}
                  onChangeText={onChange}
                />
                {error && <HelperText type="error">{error}</HelperText>}
              </>
            )}
          />
          <Stack direction="row" style={Style.agreement}>
            <Checkbox status="unchecked" />
            <Text>I hereby agree to the Terms and Conditions</Text>
          </Stack>
        </Stack>
      </CustomForm>
      <Button
        onPress={() => formRef.current?.submitForm()}
        loading={props.isLoading}
        disabled={props.isLoading}
        mode="contained"
        buttonColor="#006686"
      >
        Create Account
      </Button>
    </Stack>
  )
}

export default RegisterForm
