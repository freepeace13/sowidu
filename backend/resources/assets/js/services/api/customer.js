/** @flow */

import { get, post } from 'axios';

export const fetchCustomers = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/customers', options);
    return data.data;
}

export const fetchCustomer = async (
    customerId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/customers/${customerId}`, options);
    return data.data;
}

export const createCustomer = async (
    payload: { billerable_id: number, billerable_type: string },
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/customers`, payload, options);
    return data.data;
}