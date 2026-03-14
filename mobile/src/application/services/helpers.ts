import { FetchBaseQueryError } from "@reduxjs/toolkit/query"

export function isFetchBaseQueryError(error: unknown): error is FetchBaseQueryError {
  return typeof error === "object" && error != null && "status" in error
}

export function isErrorWithMessage(error: unknown): error is { message: string } {
  return (
    typeof error === "object" &&
    error != null &&
    "message" in error &&
    typeof (error as any).message === "string"
  )
}

export function isResponseValidationError(
  error: unknown
): error is { errors: Record<string, string[]> } {
  return typeof error === "object" && error != null && "errors" in error
}

export function getValidationErrorMessage(error: unknown, key: string) {
  const errors = getValidationErrors(error, key)
  if (Array.isArray(errors) && errors.length > 0) {
    return errors[0]
  }
}

export function getValidationErrors(error: unknown, key?: string) {
  if (isResponseValidationError(error)) {
    if (typeof key !== "string") {
      return error.errors
    }

    if (key in error.errors && Array.isArray(error.errors[key])) {
      return error.errors[key]
    }
  }
}
