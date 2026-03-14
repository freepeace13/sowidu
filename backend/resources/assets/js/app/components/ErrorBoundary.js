import VueRouter from 'vue-router';
import UnauthorizedError from '~/exceptions/UnauthorizedError';

const { isNavigationFailure, NavigationFailureType } = VueRouter;

export default {
    name: 'ErrorBoundary',

    data: () => ({
        errors: null,
        component: null,
        info: null
    }),

    errorCaptured(errors, component, info) {
        console.error('captured', errors);

        const { prevRoute } = component.$route.meta;
        
        this.handleErrorAlerts(errors);

        if (errors.redirect && prevRoute) {
            this.errors = errors;
            this.component = component;
            this.info = info;

            setTimeout(() => {
                this.$router
                    .replace(prevRoute.fullPath)
                    .catch(() => {})
                    .finally(() => {
                        this.errors = null;
                        this.component = null;
                        this.info = null;
                    });
            }, 0);
        }
        

        return false;
    },

    methods: {
        handleErrorAlerts(errors) {
            if (errors instanceof UnauthorizedError) {
                this.$events.$emit('alert', 'Access Denied');
            }
        }
    },

    created() {
        this.$router.onError((errors) => {
            this.handleErrorAlerts(errors);

            if (this.$route.path === '/') {
                this.$router.replace({ name: 'desktop' });
            }
        });
    },

    render(createElement) {
        if (this.errors === null) {
            return this.$scopedSlots.default()
        }

        // TODO: Maybe create error pages and display here..
    }
}