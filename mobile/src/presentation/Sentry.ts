import * as Sentry from "@sentry/react-native"
import { Event, EventHint } from "@sentry/types"
import axios, { AxiosError } from "axios"
import {
  SENTRY_DEBUG,
  SENTRY_DSN,
  SENTRY_ENABLED,
  SENTRY_ENVIRONMENT,
} from "@infrastructure/config"

Sentry.init({
  enabled: SENTRY_ENABLED,
  dsn: SENTRY_DSN,
  environment: SENTRY_ENVIRONMENT,
  debug: SENTRY_DEBUG,
  beforeSend: (event: Event, hint: EventHint) => {
    return addAxiosContextRecursive(event, hint?.originalException)
  },
})

function addAxiosContextRecursive(event: Event, error: unknown) {
  if (axios.isAxiosError(error)) {
    addAxiosContext(event, error)
  } else if (error instanceof Error && error.cause) {
    addAxiosContextRecursive(event, error.cause)
  }
  return event
}

function addAxiosContext(event: Event, error: AxiosError) {
  if (error.response) {
    const contexts = { ...event.contexts }
    contexts.Axios = { Response: error.response }
    event.contexts = contexts
  }
}

export default Sentry
