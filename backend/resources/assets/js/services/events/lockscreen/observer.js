import Observer from '~/services/events/observer'
import { MODULES } from '~/services/events/constants'

export default new Observer({
    modules: MODULES.LOCKSCREEN,
    events: ['close', 'stop', 'restart']
})
