import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';
import UnauthorizedError from '~/exceptions/UnauthorizedError';
import { createResource } from 'vue-async-manager';

export default {
    render(createElement) {
        return this.$scopedSlots.default();
    },

    created() {
        this.$rm = createResource(async () => {
            await this.$store
                .dispatch('auth/fetchPermissions')
                .catch((error) => {
                    if (error instanceof UnauthorizedError) {
                        this.$store.dispatch('auth/logout', 'user');
                    }
                });
        })

        this.$watch((vm) => [
            vm.$store.getters['auth/token']('user'),
            vm.$store.getters['auth/token']('company')
        ], (value) => {
            this.$rm.read()
        }, { immediate: true });
    }
}