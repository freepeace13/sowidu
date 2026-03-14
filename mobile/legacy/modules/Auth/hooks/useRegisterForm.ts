import { Utils as CoreUtils } from "core-module"
import { useState, useMemo } from "react"

import { Api as AuthApi } from "../services"

export function useRegisterForm() {
  const [register, { isLoading, error }] = AuthApi.useRegisterWithEmailMutation()
  const [data, setData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    password: "",
    confirmPassword: "",
  })

  const inputProps = (name: keyof typeof data) => {
    const errors = CoreUtils.inputValidationError(name, error)
    return {
      value: data[name],
      onChangeText: (value) => setData((prevState) => ({ ...prevState, [name]: value })),
      error: !!errors,
      errorMessage: errors,
      disabled: isLoading,
    }
  }

  console.log(error)

  const email = useMemo(() => inputProps("email"), [error, isLoading, data])
  const password = useMemo(() => inputProps("password"), [error, isLoading, data])
  const confirmPassword = useMemo(() => inputProps("confirmPassword"), [error, isLoading, data])
  const firstName = useMemo(() => inputProps("firstName"), [error, isLoading, data])
  const lastName = useMemo(() => inputProps("lastName"), [error, isLoading, data])

  return {
    isLoading,
    submit: () => register(data),
    inputProps: {
      email,
      password,
      confirmPassword,
      firstName,
      lastName,
    },
  }
}
