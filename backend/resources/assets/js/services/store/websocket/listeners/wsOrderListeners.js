/** @flow */

import Vuex from 'vuex';
import Order from '~/services/models/order';
import { cloneDeep, camelKeys } from '~/support/helpers';

export default (store: Vuex) => ({
    InsertOrder: (event: any) => {
        store.commit('order/INSERT_ORDER', Order.create(event));
    },

    UpdateOrderProgress: (event: any) => {
        const { orders } = store.state.order;

        store.commit('order/ORDER_UPDATE', Order.create({
            ...cloneDeep(orders.find((v) => v.id === event.id)),
            ...camelKeys(event)
        }));
    },
});