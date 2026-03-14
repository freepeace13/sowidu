import { createApi } from "@reduxjs/toolkit/query/react"
import * as Sentry from "@sentry/react-native"
import { selectAccessToken } from "auth-module/store/selectors"
import axios, { AxiosError } from "axios"
import omit from "lodash/omit"

import * as CoreConstants from "../../constants"

export * as Cacher from "./cacher"

export interface ApiErrorResponse {
  status: number
}

export interface ValidationError extends ApiErrorResponse {
  data: { message: string; errors: { [k: string]: string[] } }
}

export function isApiResponse(error: unknown): error is ApiErrorResponse {
  return (
    typeof error === "object" &&
    error != null &&
    "status" in error &&
    typeof (error as any).status === "number"
  )
}

export function isValidationError(error: unknown): error is ValidationError {
  return (
    isApiResponse(error) &&
    "data" in error &&
    typeof (error as any).data === "object" &&
    "errors" in (error as any).data &&
    typeof (error as any).data.errors === "object" &&
    "message" in (error as any).data &&
    typeof (error as any).data.message === "string"
  )
}

const axiosInstance = axios.create({
  baseURL: CoreConstants.API_URL,
  timeout: 10000,
  headers: {
    Accept: "application/json",
  },
})

const axiosBaseQuery = async (axiosConfig, { getState }) => {
  try {
    const axiosResponse = await axiosInstance.request({
      ...axiosConfig,
      headers: {
        ...omit(axiosConfig.headers, ["user-agent"]),
        Authorization: `Bearer ${selectAccessToken(getState())}`,
      },
    })
    return { data: axiosResponse.data?.data ?? axiosResponse.data }
  } catch (axiosError) {
    const err = axiosError as AxiosError

    if (isApiResponse(err.response)) {
      return {
        error: {
          status: err.response.status,
          data: err.response.data || err.message,
        },
      }
    }

    if (CoreConstants.SENTRY_ENABLED) {
      Sentry.captureException(err)
    }

    return {
      error: {
        message: err.message,
      },
    }
  }
}

const coreApi = createApi({
  reducerPath: "coreApi",
  baseQuery: axiosBaseQuery,
  tagTypes: ["User", "Teams"],
  endpoints: () => ({}),
})

export default coreApi
