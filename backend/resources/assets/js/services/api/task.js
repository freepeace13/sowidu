/** @flow */

import { post, patch, get } from 'axios';
import type { APIPayload } from 'task-api-payload';

export const fetchMembers = async (
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/tasks/${taskId}/members`, options);
    return data.data;
}

export const fetchDeliveries = async (
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/tasks/${taskId}/deliveries`, options);
    return data.data;
}

export const fetchOrders = async (
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/tasks/${taskId}/orders`, options);
    return data.data;
}

export const createTask = async (
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await post(`/api/tasks`, payload, options = {});
    return data.data;
}

export const updateTask = async (
    taskId: number,
    payload: APIPayload,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/tasks/${taskId}`, payload, options);
    return data.data;
}

export const fetchTask = async (
    taskId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/tasks/${taskId}`, options);
    return data.data;
}

export const fetchTasks = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get(`/api/tasks`, options);
    return data.data;
}

export const createComment = async (
    taskId: number,
    message: string,
    options: Object
): Promise<any> => {
    const { data } = await post(`/api/tasks/${taskId}/comments`, { message }, options);
    return data.data;
}
