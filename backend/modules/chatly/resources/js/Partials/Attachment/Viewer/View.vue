<script>
import ViewContainer from './ViewContainer.vue'

export default {
    components: {
        ViewContainer,
    },

    inject: ['attachment'],

    computed: {
        computedAttachment() {
            return this.attachment()
        },
    },
}
</script>
