/** @flow */

import * as types from './constants';
import CustomerService from '~/services/CustomerService';
import { Customer, User, Company, Employee } from '~/services/models';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        customers: [],
    },

    actions: {
        async create(
            { commit }: Object,
            anonymous: User | Company | Employee
        ): Promise<Customer> {
            const result: Customer = await CustomerService.create(anonymous)
            commit(types.INSERT_CUSTOMER, result);
            return result;
        },

        async all({ commit, state }: Object): Promise<any> {
            // await Cache.remember('customers', 3600, async () => {
            //     const result: Array<Customer> = await CustomerService.all();
            //     commit(types.SET_CUSTOMERS, result);
            // });
            const result: Array<Customer> = await CustomerService.all();
            commit(types.SET_CUSTOMERS, result);
            return result;
        },
    },

    mutations: {
        [types.SET_CUSTOMERS] (state: Object, customers: Array<Customer>): void {
            state.customers = customers;
        },

        [types.INSERT_CUSTOMER] (state: Object, customer: Customer): void {
            state.customers = Customer
                .collection(state.customers)
                .insert(customer)
                .all();
        },

        [types.CUSTOMER_UPDATE] (state: Object, customer: Customer): void {
            state.customers = Customer
                .collection(state.customers)
                .update(customer)
                .all();
        }
    }
}