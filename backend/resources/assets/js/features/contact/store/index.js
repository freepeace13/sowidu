/** @flow */

import * as types from './constants';
import { ContactRequest } from '~/services/models/fundamentals';
import { User, Employee, Company } from '~/services/models';
import type { Contactable } from '../api';
import ContactService from '../api';
import { ModelCollection } from '~/support/wrappers';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        contacts: [],
    },

    getters: {
        verified(state: Object) {
            return state.contacts.filter((v) => !!v.status.confirmed);
        },

        addressbook(state: Object, getters: Object) {
            return getters.verified.filter((v) => v.relations['contact:connected']);
        }
    },

    actions: {
        async all({ commit, state }: Object) {
            const result: Array<Contactable> = await ContactService.all();
            commit(types.SET_CONTACTS, result);
            return result;
        },

        async delete({ commit }: Object, contactId: number): Promise<void> {
            const result: Contactable = await ContactService.delete(contactId);
            commit(types.CONTACT_UPDATE, result); 
        }
    },

    mutations: {
        [types.SET_CONTACTS] (state: Object, contacts: Array<Contactable>) {
            state.contacts = contacts;
        },

        [types.INSERT_CONTACT] (state: Object, contact: Contactable) {
            state.contacts = (new ModelCollection(state.contacts))
                .insert(contact)
                .all();
        },

        [types.CONTACT_UPDATE] (state: Object, contact: Contactable) {
            state.contacts = (new ModelCollection(state.contacts))
                .updateOrInsert(contact)
                .all();
        }
    }
}