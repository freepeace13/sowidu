/** @flow */

import { mapState } from 'vuex';

export default () => ({
    computed: mapState({
        permissions: (state) => state.setting.permissions,
        roles: (state) => state.setting.roles
    })
});