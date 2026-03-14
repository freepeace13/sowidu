import React, { useState } from "react"
import { TextInput } from "react-native-paper"

export default function PasswordInput({
  label,
  disabled,
  value,
  onChangeText,
  error = false,
  ...props
}) {
  const [isSecuredEntry, setIsSecuredEntry] = useState(true)
  return (
    <TextInput
      {...props}
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
