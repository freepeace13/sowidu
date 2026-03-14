import axios from 'axios';
import { Service } from '@libs/core';
import Notification from './models/notification';

export class NotificationService extends Service {
    async all(options = {}) {
        const route = this.route('/notifications');

        const { data: { data } } = await axios.get(route, options);

        return data.map((v) => Notification.create(v));
    }

    async read(id, options = {}): Notification {
        const route = this.route(`/notifications/${id}/read`);

        const { data: { data } } = await axios.patch(route, {}, options);

        return Notification.create(data);
    }
}

export default new NotificationService;