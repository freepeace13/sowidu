/** @flow */

import { mapState, mapGetters } from 'vuex';
import { createContext } from '~/support/factories';
import { Company } from '~/services/models';

export default () => ({
    computed: {
        ...mapGetters({
            check: 'auth/check'
        })
    },

    created() {
        const { dispatch, getters } = this.$store;

        this.$auth = createContext({
            createCompany(payload: { company: Company, autoLogin: boolean }) {
                return dispatch('auth/createCompany', {
                    company: payload.company,
                    autoLogin: payload.autoLogin
                });
            },

            async authenticate(payload: { guard: Guard, credentials: any }) {
                await dispatch('auth/login', payload);
            },

            logout(guard: Guard) {
                dispatch('auth/logout', guard);
            },

            can(...permissions) {
                return getters['auth/authorize'](...permissions);
            }
        });
    }
})