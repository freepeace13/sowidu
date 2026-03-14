/** @flow */

import * as types from './constants';
import { Address } from '../../../models';
import AddressService from '../../../AddressService';
import type { ServicePayload } from 'address-service-payload';
import moment from 'moment';

export default {
    namespaced: true,

    state: {
        selections: [],
        active: null
    },

    getters: {
        empty(state: Object): boolean {
            if (state.persisted && !state.requesting) {
                return !state.selections && state.active === null;
            }

            return false;
        },

        all(state: Object): Array<Address> {
            return [...state.selections, state.active];
        },

        promptAddress(
            state: Object,
            getters: Object,
            rootState: Object,
            rootGetters: Object
        ): boolean {
            const { status: { skippedAddressAt } } = rootGetters['auth/profile']();
            const skippedLast = moment(skippedAddressAt);
            return getters.empty && moment().diff(skippedLast, 'days') > 3;
        },
    },

    actions: {
        async all({ commit }: Object): Promise<void> {
            const records = await AddressService.all();
            commit(types.SET_ADDRESS, records);
        },

        async skip({ commit, rootGetters }: Object): Promise<void> {
            const skippedAddressAt = await AddressService.skip();
            const guardName = rootGetters['auth/guardName'];

            commit(`auth/${guardName}/UPDATE_PROFILE`, {
                status: { skippedAddressAt }
            });
        },

        async create({ commit }: Object, instance: Address): Promise<void> {
            const address: Address = await AddressService.create(instance);
            commit(types.RESET_ACTIVE, address);
        },

        async activate({ commit }: Object, addressId: number): Promise<void> {
            const address = await AddressService.activate(addressId);
            commit(types.RESET_ACTIVE, address);
        }
    },

    mutations: {
        [types.SET_ADDRESS] (state: Object, addresses: Array<Address>) {
            state.selections = addresses.filter((v) => !v.isActive);
            state.active = addresses.filter((v) => v.isActive);
        },

        [types.RESET_ACTIVE] (state: Object, address: Address) {
            state.selections = Address
                .collection(state.selections)
                .update(address)
                .all();

            state.active = address;
        }
    },
}
