/** @flow */

import axios, { post, get, patch } from 'axios'

export const fetchPermissions = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/settings/permissions', options);
    return data.data;
}

export const fetchRoles = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/settings/roles', options);
    return data.data;
}
