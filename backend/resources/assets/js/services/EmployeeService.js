/** @flow */

import { Employee, User } from './models';
import { Permission, Role, EmploymentRequest } from './models/fundamentals';
import * as apiCalls from './api/employee';
import { callAsync } from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export class EmployeeService extends ServiceProvider {
    async all(options: Object = {}): Promise<Array<Employee>> {
        const url = this.route('/employees');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url, {
                params: { scope: 'all' },
                ...options
            });
        
            return data.data;
        });

        return result.map((v) => Employee.create(v));
    }

    async allInScope(scope: string): Promise<Array<Employee>> {
        return await this.all({ params: { scope } });
    }

    async retrieve(employeeId: number): Promise<Employee> {
        const url = this.route(`/employees/${employeeId}`);
        const { data } = await axios.get(url);
        return Employee.create(data.data);
    }

    async updatePermissions(
        employeeId: number,
        permissions: Array<Permission>,
        roles: Array<Role>
    ): Promise<Employee> {

        const url = this.route(`/employees/${employeeId}/roles-and-permissions`);

        const { data } = await axios.patch(url, {
            roles: Role.collection(roles).pluck('id').all(),
            permissions: Permission.collection(permissions).pluck('id').all()
        });

        return Employee.create(data.data);
    }
}

export default new EmployeeService;