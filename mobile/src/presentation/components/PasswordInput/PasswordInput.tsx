import React, { forwardRef, useState } from "react"
import { TextInput, TextInputProps } from "react-native-paper"

type PasswordInputPropsType = TextInputProps

function PasswordInput(props: PasswordInputPropsType) {
  const { label, disabled, value, onChangeText, error = false, ...restProps } = props
  const [isSecuredEntry, setIsSecuredEntry] = useState(true)
  return (
    <TextInput
      {...restProps}
      label={label}
      mode="outlined"
      activeOutlineColor="#006686"
      outlineColor="#71787D"
      textColor="#40484C"
      textContentType="password"
      autoCapitalize="none"
      autoComplete="off"
      inputMode="text"
      disabled={disabled}
      value={value}
      error={error}
      secureTextEntry={isSecuredEntry}
      onChangeText={onChangeText}
      right={
        <TextInput.Icon
          icon={isSecuredEntry ? "eye" : "eye-off"}
          onPress={() => setIsSecuredEntry((value) => !value)}
        />
      }
    />
  )
}

export default PasswordInput
