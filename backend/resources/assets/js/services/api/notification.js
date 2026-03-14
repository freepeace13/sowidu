/** @flow */

import { get, patch } from 'axios';

export const fetchNotifications = async (
    options: Object = {}
): Promise<any> => {
    const { data } = await get('/api/notifications', options);
    return data.data;
}

export const readNotification = async (
    notificationId: number,
    options: Object = {}
): Promise<any> => {
    const { data } = await patch(`/api/notifications/${notificationId}/read`, options);
    return data.data;
}