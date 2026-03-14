import Observer from '~/services/events/observer'
import { MODULES } from '~/services/events/constants'

export default new Observer({
    modules: MODULES.MODAL,
    events: [
        'show',
        'append',
        'close',
        'closeAll',
        'clear'
    ]
})
