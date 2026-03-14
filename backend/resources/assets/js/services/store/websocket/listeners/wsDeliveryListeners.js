/** @flow */

import Vuex from 'vuex';
import Delivery from '~/services/models/delivery';

export default (store: Vuex) => ({
    InsertDelivery: (event: any) => {
        store.commit('delivery/INSERT_DELIVERY', Delivery.create(event));
    },

    DeliveryUpdate: (event: any) => {
        store.commit('delivery/DELIVERY_UPDATE', Delivery.create(event));
    },
});