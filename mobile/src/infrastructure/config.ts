import * as Updates from "expo-updates"

export const APP_NAME = "Sowidu"
export const APP_DEBUG = true
export const APP_ENV = Updates.channel || "development"

export const SENTRY_DEBUG = false
export const SENTRY_ENABLED = !["production", "staging"].includes(APP_ENV)
export const SENTRY_ENVIRONMENT = "staging"
export const SENTRY_DSN =
  "https://f64af01a64cd5f5fb3de4a3ae4705ee2@o4505664712343552.ingest.sentry.io/4505664720470016"

export const API_BASE_URL = "https://staging.sowidu.de"
export const API_DEBUG = true
export const API_TIMEOUT = 20000

export const MEDIA_UPLOAD_URI = `${API_BASE_URL}/api/v1/media/upload`
