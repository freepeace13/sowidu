/** @flow */

import { mapState, mapGetters } from 'vuex';
import { createContext } from '~/support/factories';
import { Delivery } from '~/services/models';
import DeliveryService from '~/services/DeliveryService';

export default () => ({
    computed: {
        ...mapState({
            deliveries: (state) => state.delivery.deliveries
        }),

        ...mapGetters({
            incoming: 'delivery/incoming',
            outgoing: 'delivery/outgoing'
        })
    },

    created() {
        const { dispatch } = this.$store;

        this.$deliveries = createContext({
            create(instance: Delivery) {
                return DeliveryService.create(instance);
            },
            update(instance: Delivery) {
                return DeliveryService.update(instance);
            },
        });
    }
});