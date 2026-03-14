

export default {
    methods: {
        lockScreenStart() {
            this.$inertia.post(this.$route('lockscreen.activate'));
        },

        lockScreenStop(password) {
            this.$inertia.post(this.$route('lockscreen.deactivate'), { data: { password } });
        },
    },
};