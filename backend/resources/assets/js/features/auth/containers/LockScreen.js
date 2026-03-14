import LockScreenForm from '../components/LockScreenForm';
import { TogglesLockScreen } from '~/components/Mixins';
import { AUTH_GUARDS } from '~/support/constants';

export default {
    data: () => ({
        timer: null,
        idletime: 30, // minutes
        elapsed: 0,
    }),

    mixins: [TogglesLockScreen()],

    methods: {
        start() {
            // Run watcher every 1 minute
            this.timer = setInterval(() => {
                this.elapsed += 1

                if (this.elapsed >= this.idletime && !this.isLocked) {
                    this.$screen.lock();
                    this.stop();
                    this.reset();
                }
            }, 60000);
        },

        stop() {
            clearInterval(this.timer);
        },

        reset() {
            this.elapsed = 0;
        },

        restart() {
            if (this.timer !== null) {
                this.stop();
            }

            this.reset();
            this.start();
        },

        breakIdle () {
            !this.isScreenLocked && this.restart();
        }
    },

    created() {
        document.addEventListener('mousemove', this.breakIdle);
        document.addEventListener('keypress', this.breakIdle);
    },

    render(createElement) {
        if (this.isScreenLocked) {
            return createElement(LockScreenForm, {
                props: {
                    loading: this.$screen.$loading,
                    errors: this.$screen.$errors,
                    unlock: this.$screen.unlock,
                    profile: this.$store.getters['auth/profile'](AUTH_GUARDS.USER)
                }
            });
        }

        return this.$scopedSlots.default();
    }
}