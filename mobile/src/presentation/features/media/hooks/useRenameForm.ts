import { validationErrors } from "@presentation/app/utils"
import { useState } from "react"
import { useForm } from "react-hook-form"

type RenameInputs = {
  name: string
}

export const useRenameForm = (defaultValues?: RenameInputs) => {
  const [isLoading, setIsLoading] = useState(false)
  const {
    setValue: formSetValue,
    getValues: formGetValue,
    handleSubmit,
    clearErrors,
    setError,
    formState: { errors },
  } = useForm<RenameInputs>({
    mode: "onSubmit",
    defaultValues,
  })

  const getValue = (name: keyof RenameInputs) => {
    return formGetValue(name)
  }

  const setValue = (name: keyof RenameInputs, value: any) => {
    formSetValue(name, value, {
      shouldValidate: false,
      shouldTouch: false,
      shouldDirty: false,
    })
  }

  const onSubmit = handleSubmit(async (data) => {
    setIsLoading(true)
    try {
      console.log(data.name)
    } catch (error) {
      validationErrors(error).spread((key, message) => {
        setError(key, { type: "custom", message })
      })
    } finally {
      setIsLoading(false)
    }
  })

  return {
    onSubmit,
    isLoading,
    clearErrors,
    control: {
      errors,
      setValue,
      getValue,
    },
  }
}
