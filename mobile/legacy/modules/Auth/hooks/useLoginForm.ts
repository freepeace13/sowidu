import { Utils as CoreUtils } from "core-module"
import { useState, useMemo } from "react"

import { Api as AuthApi } from "../services"

interface Credentials {
  email?: string | undefined
  password?: string | undefined
}

export function useLoginForm({ email = "", password = "" }: Credentials = {}) {
  const [login, { isLoading, error }] = AuthApi.useLoginWithEmailMutation()
  const [credentials, setCredentials] = useState({
    email,
    password,
  })

  const inputProps = (name: keyof typeof credentials) => {
    const errors = CoreUtils.inputValidationError(name, error)
    return {
      value: credentials[name],
      onChangeText: (value) => setCredentials((prevState) => ({ ...prevState, [name]: value })),
      error: !!errors,
      errorMessage: errors,
      disabled: isLoading,
    }
  }

  const emailInputProps = useMemo(() => inputProps("email"), [error, isLoading, credentials])
  const passwordInputProps = useMemo(() => inputProps("password"), [error, isLoading, credentials])

  return {
    isLoading,
    submit: () => login(credentials),
    inputProps: {
      email: emailInputProps,
      password: passwordInputProps,
    },
  }
}
