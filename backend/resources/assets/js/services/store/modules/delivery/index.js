/** @flow */

import * as types from './constants';
import { Order, Employee, Task, Delivery, Media } from '~/services/models';
import DeliveryService from '~/services/DeliveryService';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        deliveries: []
    },

    getters: {
        incoming(state: Object) {
            return state.deliveries.filter((v) => v.type === 'incoming');
        },

        outgoing(state: Object) {
            return state.deliveries.filter((v) => v.type === 'outgoing');
        }
    },

    actions: {
        async all({ commit, state }: Object): Promise<any> {
            // await Cache.remember('deliveries', 3600, async () => {
            //     const result: Array<Delivery> = await DeliveryService.all();
            //     commit(types.SET_DELIVERIES, result);
            // });
            const result: Array<Delivery> = await DeliveryService.all();
            commit(types.SET_DELIVERIES, result);
            return result;
        },
    },

    mutations: {
        [types.SET_DELIVERIES] (state: Object, deliveries: Array<Delivery>): void {
            state.deliveries = deliveries;
        },

        [types.INSERT_DELIVERY] (state: Object, delivery: Delivery): void {
            state.deliveries = Delivery
                .collection(state.deliveries)
                .insert(delivery)
                .all();
        },

        [types.DELIVERY_UPDATE] (state: Object, delivery: Delivery): void {
            state.deliveries = Delivery
                .collection(state.deliveries)
                .updateOrInsert(delivery)
                .all();
        }
    }
}