/** @flow */

import * as types from './constants';
import EmployeeService from '../api';
import { Employee } from '~/services/models';
import { Permission, Role, EmploymentRequest } from '~/services/models/fundamentals';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        employees: []
    },

    actions: {
        async all({ commit, state }: Object): Promise<any> {
            const result = await EmployeeService.allInScope('hired');
            commit(types.SET_EMPLOYEES, result);
            return result;
        },

        async updateAccessRoles(context: Object, payload: Object): Promise<void> {
            const result = await EmployeeService.updateAccessRoles(
                payload.employeeId, payload.roles
            );
            context.commit(types.EMPLOYEE_UPDATE, result);
            return result;
        }
    },

    mutations: {
        [types.SET_EMPLOYEES] (state: Object, employees: Array<Employee>) {
            state.employees = employees
        },

        [types.INSERT_EMPLOYEE] (state: Object, employee: Employee) {
            state.employees = Employee
                .collection(state.employees)
                .insert(employee)
                .all();
        },

        [types.EMPLOYEE_UPDATE] (state: Object, employee: Employee) {
            state.employees = Employee
                .collection(state.employees)
                .update(employee)
                .all();
        }
    }
}
