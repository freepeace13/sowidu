/** @flow */

import axios, { post, get, patch } from 'axios';
import { getStateRoute } from './utils';
import type { APIPayload } from 'order-api-payload';

export const fetchOrder = async (
    orderId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/orders/${orderId}`, options);
    return data.data;
}

export const fetchOrderStates = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/orders/statuses`, options);
    return data.data;
}

export const changeState = async (
    orderId: number,
    state: OrderStateType,
    options: Object = {}
): Promise<any> => {
    const route = getStateRoute(state);
    const { data } = await patch(`/api/orders/${orderId}/${route}`, {}, options);
    return data.data;
}

export const cancelOrder = async (
    orderId: number,
    options: Object = {}
): Promise<any> => {
    return await patch(`/api/orders/${orderId}/cancel`, options);
}

export const confirmOrder = async (
    orderId: number,
    options: Object = {}
): Promise<any> => {
    return await patch(`/api/orders/${orderId}/confirm`, options);
}

export const fetchOrders = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/orders`, options);
    return data.data;
}

export const fetchByType = async (
    type: OrderType = 'incoming',
    state: OrderStateType = 'preparation',
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/${type}-orders`, { ...options, params: { state } });
    return data.data;
}

export const updateByType = async (
    orderId: number,
    type: OrderType = 'incoming',
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/${type}-orders/${orderId}`, payload, options);
    return data.data;
}

export const createByType = async (
    type: OrderType,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/${type}-orders`, {}, options);
    return data.data;
}

// Deliveries

export const fetchDeliveries = async (
    orderId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/orders/${orderId}/deliveries`, options);
    return data.data;
}

export const createDelivery = async (
    orderId: number,
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/orders/${orderId}/deliveries/${deliveryId}`, options);
    return data.data;
}

export const removeDelivery = async (
    orderId: number,
    deliveryId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await axios.delete(`/api/orders/${orderId}/deliveries/${deliveryId}`, 
    options);
    return data.data;
}

// Tasks

export const createTask = async (
    orderId: number,
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/orders/${orderId}/tasks/${taskId}`, options);
    return data.data;
}

export const removeTask = async (
    orderId: number,
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await axios.delete(`/api/orders/${orderId}/deliveries/${taskId}`, options)
    return data.data;;
}

// Order Members

export const updateMembers = async (
    orderId: number,
    members: Array<number>,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/orders/${orderId}/members`, { members }, options);
    return data.data;
}