import Observer from '~/services/events/observer'
import { MODULES } from '~/services/events/constants'

export default new Observer({
    modules: MODULES.CHUNK_UPLOADER,
    events: ['upload', 'close']
});
