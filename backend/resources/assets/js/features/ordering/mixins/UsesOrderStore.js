/** @flow */

import { createContext } from '~/support/factories';
import { mapState, mapGetters } from 'vuex';
import { Order } from '~/services/models';

export default () => ({
    computed: {
        ...mapState({
            orders: (state) => state.order.orders
        }),

        ...mapGetters({
            incoming: 'order/incoming',
            outgoing: 'order/outgoing'
        })
    },

    created() {
        const { $router, $store } = this;

        this.$orders = createContext({
            async create(type: OrderType) {
                const result = await $store.dispatch('order/create', type);
                $router.push({ name: 'orders.show', params: { id: result.id } });
            }
        })
    }
});