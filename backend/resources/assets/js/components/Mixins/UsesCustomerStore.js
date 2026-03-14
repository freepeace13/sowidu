/** @flow */

import { mapState } from 'vuex';
import { createContext } from '~/support/factories';
import { User, Company, Employee } from '~/services/models';

export default () => ({
    computed: {
        ...mapState({
            customers: (state) => state.customer.customers
        })
    },

    created() {
        const { dispatch } = this.$store;

        this.$customers = createContext({
            create(contactable: User | Employee | Company) {
                return dispatch('customer/create', contactable);
            }
        });
    }
});