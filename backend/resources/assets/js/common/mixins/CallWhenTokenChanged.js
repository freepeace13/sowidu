export default (callback) => ({
    created() {
        this.$watch((vm) => [
            vm.$store.getters['auth/token']('user'),
            vm.$store.getters['auth/token']('company')
        ], (value) => {
            this.$nextTick(() => callback.apply(this));
        }, { immediate: true });
    }
})