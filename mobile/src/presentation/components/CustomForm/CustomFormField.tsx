import { FunctionComponent, useEffect } from "react"
import { useFormContext } from "react-hook-form"

interface FieldProps {
  name: string
  defaultValue?: any
  render: (field: { value: any; onChange: (value: any) => void; error?: string }) => React.ReactNode
}

const CustomFormField: FunctionComponent<FieldProps> = ({ name, defaultValue, render }) => {
  const { register, unregister, setValue, getValues, clearErrors, formState } = useFormContext()
  const error = formState.errors[name]?.message as string | undefined

  useEffect(() => {
    register(name)
    if (defaultValue !== undefined) {
      setValue(name, defaultValue, { shouldValidate: false })
    }
    return () => {
      unregister(name) // Unregister the field on unmount to avoid memory leaks
    }
  }, [name, defaultValue, register, setValue, unregister])

  // Watch value changes
  const value = getValues(name) ?? defaultValue

  return render({
    value,
    onChange: (value: any) => {
      setValue(name, value, { shouldValidate: false })
      clearErrors(name) // Clear error on change
    },
    error,
  })
}

export default CustomFormField
