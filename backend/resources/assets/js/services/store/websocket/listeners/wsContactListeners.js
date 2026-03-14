/** @flow */

import Vuex from 'vuex';

import UserService from '~/services/UserService';
import CompanyService from '~/services/CompanyService';
import EmployeeService from '~/services/EmployeeService';

import { resolveFromRaw } from '~/services/models';

const mapServices = {
    companies: CompanyService,
    employees: EmployeeService,
    users: UserService
}

export default (store: Vuex) => ({
    ContactUpdate: async (event: any) => {
        const service = mapServices[event.alias];
        const contact = await service.retrieve(event.id);
        store.commit('contact/CONTACT_UPDATE', contact);
    },

    InsertContact: (event: any) => {
        store.commit('contact/INSERT_CONTACT', resolveFromRaw(event));
    },
});