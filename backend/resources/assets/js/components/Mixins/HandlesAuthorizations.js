/** @flow */

import { mapGetters } from 'vuex';
import { User } from '~/services/models';

export default () => ({
    computed: {
        ...mapGetters({
            profile: 'auth/profile',
            authorizeTo: 'auth/authorize'
        }),

        currentIsUser() {
            return this.profile() instanceof User;
        }
    }
})