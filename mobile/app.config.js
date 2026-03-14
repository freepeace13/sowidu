const APP_NAME = process.env.EXPO_PUBLIC_APP_NAME || "Sowidu"
const APP_VARIANT = process.env.EXPO_PUBLIC_APP_VARIANT || "com.sowidu"

/** @type {import('expo/config').ExpoConfig} */
const appConfig = {
  name: APP_NAME,
  slug: "sowidu",
  version: "1.0.0",
  icon: "./assets/images/icon-fill-background.png",
  platforms: ["ios", "android"],
  runtimeVersion: {
    policy: "nativeVersion",
  },
  android: {
    package: APP_VARIANT,
    adaptiveIcon: {
      foregroundImage: "./assets/images/adaptive-icon.png",
      backgroundColor: "#006686",
    },
  },
  ios: {
    supportsTablet: true,
    bundleIdentifier: APP_VARIANT,
  },
  assetBundlePatterns: ["./assets/images/*"],
  splash: {
    backgroundColor: "#006686",
    image: "./assets/images/splash-white.png",
    resizeMode: "contain",
  },
  plugins: [
    "expo-localization",
    "@config-plugins/react-native-blob-util",
    "@config-plugins/react-native-pdf",
    ["expo-screen-orientation", { initialOrientation: "DEFAULT" }],
    [
      "expo-image-picker",
      {
        photosPermission: "The app accesses your photos to let you share them with your friends.",
      },
    ],
    [
      "expo-document-picker",
      {
        iCloudContainerEnvironment: "Production",
      },
    ],
    [
      "expo-camera",
      {
        cameraPermission: "Allow $(PRODUCT_NAME) to access your camera.",
      },
    ],
    [
      "expo-media-library",
      {
        photosPermission: "Allow $(PRODUCT_NAME) to access your photos.",
        savePhotosPermission: "Allow $(PRODUCT_NAME) to save photos.",
        isAccessMediaLocationEnabled: true,
      },
    ],
    [
      "expo-navigation-bar",
      {
        position: "relative",
        visibility: "visible",
        behavior: "inset-touch",
      },
    ],
    [
      "@sentry/react-native/expo",
      {
        organization: "sowidu",
        project: "sowidu",
      },
    ],
  ],
  updates: {
    enabled: true,
    checkAutomatically: "ON_LOAD",
    fallbackToCacheTimeout: 300000,
    url: "https://u.expo.dev/45e63a97-dfc7-4be4-a921-cd904496c553",
  },
  extra: {
    eas: {
      projectId: "45e63a97-dfc7-4be4-a921-cd904496c553",
    },
  },
  experiments: {
    tsconfigPaths: true,
  },
}

module.exports = appConfig
