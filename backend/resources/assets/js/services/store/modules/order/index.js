/** @flow */

import * as types from './constants';
import { Order, Employee, Task, Delivery } from '~/services/models';
import OrderService from '~/services/OrderService';
import { mutateTasks, mutateDeliveries } from './utils';
import sketch from './sketch';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    modules: { sketch },

    state: {
        orders: [],
    },

    getters: {
        incoming(state: Object) {
            return state.orders.filter((v) => v.policies.isType('incoming'));
        },
        
        outgoing(state: Object) {
            return state.orders.filter((v) => v.policies.isType('outgoing'));
        }
    },

    actions: {
        async all({ commit, state }: Object): Promise<any> {
            // await Cache.remember('orders', 3600, async () => {
            //     const result: Array<Order> = await OrderService.all();
            //     commit(types.SET_ORDERS, result);
            // });
            const result: Array<Order> = await OrderService.all();
            commit(types.SET_ORDERS, result);
            return result;
        },

        async create({ commit }: Object, type: OrderType): Promise<Order> {
            const result: Order = await OrderService.create(type);
            commit(types.INSERT_ORDER, result);
            return result;
        },

        async update({ commit }: Object, order: Order): Promise<Order> {
            const result: Order = await OrderService.update(order);

            commit(types.ORDER_UPDATE, result);

            return result;
        },
    },

    mutations: {
        [types.SET_ORDERS] (state: Object, orders: Array<Order>): void {
            state.orders = orders;
        },

        [types.INSERT_ORDER] (state: Object, order: Order): void {
            state.orders = Order
                .collection(state.orders)
                .insert(order)
                .all();
        },

        [types.ORDER_UPDATE] (state: Object, order: Order): void {
            state.orders = Order
                .collection(state.orders)
                .update(order)
                .all();
        }
    }
}