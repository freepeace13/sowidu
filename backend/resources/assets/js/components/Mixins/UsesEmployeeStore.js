/** @flow */

import { mapActions, mapState } from 'vuex';

export default () => ({
    computed: {
        ...mapState({
            employees: (state) => state.employee.employees
        })
    },

    methods: {
        ...mapActions({
            updatePermissions: 'employee/updatePermissions'
        })
    }
});