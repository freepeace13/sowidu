import { TextInputProps, TextInput } from "react-native-paper"

type EmailInputPropsType = TextInputProps

function EmailInput(props: EmailInputPropsType) {
  return (
    <TextInput
      {...props}
      mode="outlined"
      outlineColor="#71787D"
      textColor="#40484C"
      autoComplete="email"
      keyboardType="email-address"
      textContentType="emailAddress"
      autoCapitalize="none"
    />
  )
}

export default EmailInput
