import { getLocales } from "expo-localization"
import { I18n } from "i18n-js"

import { de, en } from "./translations"

const i18n = new I18n({
  en,
  de,
})

i18n.locale = getLocales()[0].languageCode
i18n.enableFallback = true
i18n.missingBehavior = "guess"

export default i18n
