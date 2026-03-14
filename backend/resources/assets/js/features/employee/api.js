/** @flow */

import { Employee, User } from '~/services/models';
import { Permission, Role, EmploymentRequest } from '~/services/models/fundamentals';
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

    async updateAccessRoles(employeeId: number, roles: Array<Role>): Promise<Employee> {
        const url = this.route(`/employees/${employeeId}/access-roles`);

        const { data } = await axios.patch(url, {
            roles: roles.map((v) => v.id),
        });

        return Employee.create(data.data);
    }
}

export default new EmployeeService;