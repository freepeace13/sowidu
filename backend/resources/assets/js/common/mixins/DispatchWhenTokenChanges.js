import { createResource } from 'vue-async-manager';

export default (...params) => ({
    created() {
        this.$rm = createResource((...params) => {
            return this.$store.dispatch(...params)
        });

        this.$watch((vm) => [
            vm.$store.getters['auth/token']('user'),
            vm.$store.getters['auth/token']('company')
        ], (value) => {
            this.$rm.read(...params);
        }, { immediate: true });
    }
})