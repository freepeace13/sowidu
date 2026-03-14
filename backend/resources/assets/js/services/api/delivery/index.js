/** @flow */

import { get, post, patch } from 'axios';
import type { APIPayload } from 'delivery-api-payload';

export const fetchByType = async (
    type: DeliveryType = 'incoming',
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/deliveries/${type}`, options);
    return data.data;
}

export const updateByType = async (
    deliveryId: number,
    type: DeliveryType = 'incoming',
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/deliveries/${type}/${deliveryId}`, payload, options);
    return data.data;
}

export const createByType = async (
    type: DeliveryType = 'incoming',
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/deliveries/${type}`, payload, options);
    return data.data;
}

export const fetchDelivery = async (
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/deliveries/${deliveryId}`, options);
    return data.data;
}

export const fetchDeliveries = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/deliveries', options);
    return data.data;
}

// Delivery Tasks

export const fetchTasks = async (
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/deliveries/${deliveryId}/tasks`, options);
    return data.data;
}

// Delivery Orders

export const fetchOrders = async (
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/deliveries/${deliveryId}/orders`, options);
    return data.data;
}

// Delivery Media

export const fetchMedia = async (
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/deliveries/${deliveryId}/media`, options);
    return data.data;
}