/** @flow */

import Vuex from 'vuex';
import Employee from '~/services/models/employee';

export default (store: Vuex) => ({
    InsertEmployee: (event: any) => {
        store.commit('employee/INSERT_EMPLOYEE', Employee.create(event));
    }
});