/** @flow */

import { get } from 'axios';

export const fetchUsers = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/users`, options);
    return data.data;
} 

export const fetchUser = async (
    userId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/users/${userId}`, options);
    return data.data;
}

export const fetchUserEmployments = async (
    userId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/users/${userId}/employments`, options);
    return data.data;
}