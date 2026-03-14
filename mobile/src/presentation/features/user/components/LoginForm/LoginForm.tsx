import EmailInput from "@presentation/components/EmailInput/EmailInput"
import PasswordInput from "@presentation/components/PasswordInput/PasswordInput"
import Stack from "@presentation/components/Stack/Stack"
import { Keyboard } from "react-native"
import { Text, Button, useTheme, HelperText } from "react-native-paper"

import Style from "./LoginFormStyle"
import ForgotPasswordLink from "../ForgotPasswordLink/ForgotPasswordLink"
import RegisterLink from "../RegisterLink/RegisterLink"
import { CustomForm, CustomFormField } from "@presentation/components/CustomForm"
import { useRef } from "react"
import { FormRef } from "@presentation/components/CustomForm/CustomForm"

type LoginFormPropsType = {
  title: string
  isLoading?: boolean
  onForgotPassword: () => any
  onRegister: () => any
  onLogin: (values: { email: string; password: string }) => Promise<void>
}

function LoginForm(props: LoginFormPropsType) {
  const { colors } = useTheme()
  const formRef = useRef<FormRef>(null)
  return (
    <Stack direction="column" space={0}>
      <Text variant="bodyMedium" style={Style.title}>
        {props.title}
      </Text>
      <CustomForm ref={formRef} onSubmit={props.onLogin}>
        <Stack direction="column" style={Style.formStack}>
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
                  onSubmitEditing={Keyboard.dismiss}
                  value={value}
                  onChangeText={onChange}
                />
                {error && <HelperText type="error">{error}</HelperText>}
              </>
            )}
          />
          <ForgotPasswordLink label="Forgot password?" onPress={props.onForgotPassword} />
          <Button
            mode="contained"
            buttonColor={colors.primary}
            loading={props.isLoading}
            disabled={props.isLoading}
            onPress={() => formRef.current?.submitForm()}
          >
            Log in
          </Button>
        </Stack>
      </CustomForm>
      <RegisterLink
        title="Do you have any account?"
        label="Create Account"
        onPress={props.onRegister}
      />
    </Stack>
  )
}

export default LoginForm
