import { API_BASE_URL, API_DEBUG, API_TIMEOUT, APP_DEBUG } from "@infrastructure/config"
import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from "axios"
import * as Sentry from "@sentry/react-native"
import { withAuthorizationHeader } from "@infrastructure/utils/withAuthorizationHeader"

const ResponseStatusCode = {
  NotFound: 404,
  Unauthorized: 401,
  Forbidden: 403,
  BadRequest: 400,
  UnprocessableEntity: 422,
  ServerError: 500,
}

const axiosInstance: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT,
  headers: {
    Accept: "application/json",
  },
})

axiosInstance.interceptors.request.use(async (config: AxiosRequestConfig) => {
  config = await withAuthorizationHeader(config)
  // config = await withRequestLogger(config)
  return config
})

axiosInstance.interceptors.response.use(
  async (response: AxiosResponse) => await Promise.resolve(response),
  async (error: any) => {
    if (API_DEBUG) {
      console.log(JSON.stringify(error))
    }

    error = await withSentryCaptureException(error)
    error = await withErrorTransformer(error)
    return Promise.reject(error)
  }
)

const withErrorTransformer = (error: any) => {
  if ("response" in error && "data" in error.response) {
    const { status, data } = error.response

    error = {
      code: status,
      message: data?.message,
    }

    if (ResponseStatusCode.UnprocessableEntity === status) {
      error = { ...error, errors: data.errors }
    }
  }

  return error
}

const withSentryCaptureException = (error: any) => {
  const dontReportStatus = [
    ResponseStatusCode.NotFound,
    ResponseStatusCode.Unauthorized,
    ResponseStatusCode.Forbidden,
    ResponseStatusCode.UnprocessableEntity,
  ]

  if (
    "response" in error &&
    "status" in error.response &&
    typeof error.response.status === "number" &&
    !dontReportStatus.includes(error.response.status)
  ) {
    Sentry.captureException(error)
  }

  return error
}

const withRequestLogger = async (config: AxiosRequestConfig) => {
  if (API_DEBUG) {
    console.info("API Request: ", config)
  }

  return config
}

export { axiosInstance }
