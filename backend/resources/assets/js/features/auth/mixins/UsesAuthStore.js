/** @flow */

import { mapState, mapGetters } from 'vuex';

export default () => ({
    computed: {
        ...mapState({
            companies: (state) => state.auth.companies,
        }),

        ...mapGetters({
            check: 'auth/check',
            profile: 'auth/profile',
            token: 'auth/token'
        })
    }
})