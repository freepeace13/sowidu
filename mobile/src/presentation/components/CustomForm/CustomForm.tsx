import { getValidationErrorMessage, isResponseValidationError } from "@application/services/helpers"
import React, { forwardRef, useImperativeHandle } from "react"
import { useForm, FormProvider } from "react-hook-form"

interface CustomFormProps {
  children: React.ReactNode
  onSubmit: (values: any) => Promise<void>
  defaultValues?: Record<string, any>
}

export interface FormRef {
  submitForm: () => void
  setCustomErrors: (errors: { [key: string]: string }) => void
}

const CustomForm = forwardRef<FormRef, CustomFormProps>(
  ({ children, onSubmit, defaultValues }, ref) => {
    const methods = useForm({
      mode: "onBlur", // No built-in validation
      defaultValues,
    })

    const setCustomErrors = (errors: { [key: string]: string }) => {
      Object.entries(errors).forEach(([field, message]) => {
        methods.setError(field, { type: "custom", message })
      })
    }

    const handleFormSubmit = async (data: any) => {
      methods.clearErrors() // Clear previous errors
      try {
        await onSubmit(data)
      } catch (e) {
        if (isResponseValidationError(e)) {
          setCustomErrors(
            Object.keys(e.errors).reduce((err: { [key: string]: string }, key) => {
              const error = getValidationErrorMessage(e, key)
              if (error) err[key] = error
              return err
            }, {})
          )
        }
      }
    }

    // Expose methods via ref
    useImperativeHandle(ref, () => ({
      submitForm: () => {
        methods.handleSubmit(handleFormSubmit)() // Call handleSubmit programmatically
      },
      setCustomErrors,
    }))

    return <FormProvider {...methods}>{children}</FormProvider>
  }
)

CustomForm.displayName = "CustomForm"

export default CustomForm
