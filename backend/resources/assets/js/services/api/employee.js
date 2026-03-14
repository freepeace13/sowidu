/** @flow */

import { get, post, patch } from 'axios';
import type { RolePermissionPayload } from 'auth-role-permission-payload';

export const fetchEmployees = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/employees', options);
    return data.data;
}

export const fetchEmployee = async (
    employeeId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/employees/${employeeId}`, options);
    return data.data;
}

export const updatePermissions = async (
    employeeId: number,
    permissions: Array<number>,
    roles: Array<number>,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/employees/${employeeId}/roles-and-permissions`, {
        roles, permissions
    }, options);

    return data.data;
}