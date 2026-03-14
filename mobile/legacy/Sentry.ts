import * as Sentry from "@sentry/react-native"
import { Event, EventHint } from "@sentry/types"
import axios, { AxiosError } from "axios"
import { Constants as CoreConstants } from "core-module"
import * as Updates from "expo-updates"

const SENTRY_ENABLED: boolean =
  CoreConstants.SENTRY_ENABLED ??
  CoreConstants.Environments.except("development").includes(Updates.channel)

Sentry.init({
  enabled: SENTRY_ENABLED,
  dsn: "https://f64af01a64cd5f5fb3de4a3ae4705ee2@o4505664712343552.ingest.sentry.io/4505664720470016",
  environment: Updates.channel || "development",
  debug: Updates.channel !== "production",
  beforeSend: onBeforeSend,
})

function onBeforeSend(event: Event, hint: EventHint) {
  return addAxiosContextRecursive(event, hint?.originalException)
}

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
