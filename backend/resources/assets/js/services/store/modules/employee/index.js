/** @flow */

import * as types from './constants';
import EmployeeService from '~/services/EmployeeService';
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
            // await Cache.remember('employees', 3600, async () => {
            //     const result = await EmployeeService.allInScope('hired');
            //     commit(types.SET_EMPLOYEES, result);
            // });
            const result = await EmployeeService.allInScope('hired');
            commit(types.SET_EMPLOYEES, result);
            return result;
        },

        async update(
            { commit }: Object,
            { employeeId, permissions, roles }: {
                employeeId: number,
                permissions: Array<Permission>,
                roles: Array<Role>
            }
        ): Promise<void> {
            const result: Employee = await EmployeeService.updatePermissions(
                employeeId, permissions, roles
            );

            commit(types.EMPLOYEE_UPDATE, result);
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
