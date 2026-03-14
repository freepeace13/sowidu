import { onBeforeUnmount, onMounted } from 'vue'
import { EventBus } from '~Invoicify/Services/EventBus'

export const useEventListener = (event, handler) => {
    onBeforeUnmount(() => {
        EventBus.$on(event, handler)
    })

    onMounted(() => {
        EventBus.$on(event, handler)
    })
}
