import { DEFAULT_CONFIG, MODULES } from '~/services/events/constants'
import _terminal from '~/services/events'

export const $snackbar = _terminal.snackbar

export const defaultConfig = DEFAULT_CONFIG[MODULES.SNACKBAR]
