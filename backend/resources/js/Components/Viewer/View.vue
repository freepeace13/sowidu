<script>
import ViewContainer from './ViewContainer.vue';

export default {
    inject: ['media'],

    components: {
        ViewContainer,
    },

    computed: {
        computedMedia() {
            return this.media();
        },
    },
};
</script>
