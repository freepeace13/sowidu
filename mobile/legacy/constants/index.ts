export const API_URL = process.env.EXPO_PUBLIC_API_URL

/** SENTRY config */
export const SENTRY_ENABLED = process.env.EXPO_PUBLIC_SENTRY_ENABLED === "true"

//

class AppEnv {
  stages: string[]
  constructor(stages) {
    this.stages = stages
  }
  except(...env) {
    return this.stages.filter((i) => !env.includes(i))
  }
}

export const Environments = new AppEnv(["production", "staging", "development"])

//

export const RouteNames = {
  HomeNavigator: "HomeNavigator",
  Home: "HomeScreen",
  Notification: "NotificationScreen",
}

export default {
  //
  API_URL,
  SENTRY_ENABLED,

  //
  RouteNames,
  Environments,
}
