/** @flow */

import { mapState, mapGetters } from 'vuex';
import { MessageBag } from '~/support/wrappers';
import AuthService from '~/services/AuthService';
import { createContext } from '~/support/factories';

export default () => ({
    computed: mapState({
        isScreenLocked: (state) => state.ui.screenLocked
    }),

    created() {
        const { commit } = this.$store;

        this.$screen = createContext({
            async unlock(password: string) {
                await AuthService.confirmPassword(password);
                commit('ui/TOGGLE_LOCKSCREEN', false);
            },
            lock() {
                commit(`ui/TOGGLE_LOCKSCREEN`, true);
            },
        });
    }
})