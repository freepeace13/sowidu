/** @flow */

import { mapState } from 'vuex';

export default () => ({
    computed: {
        ...mapState({
            notifications: (state) => state.notification.notifications
        })
    }
})