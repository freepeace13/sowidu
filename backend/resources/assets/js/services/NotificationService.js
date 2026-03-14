/** @flow */

import * as apiCalls from './api/notification';
import { Notification } from './models';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export class NotificationService extends ServiceProvider {
    async all(): Promise<Array<Notification>> {
        const { data } = await axios.get(this.route('/notifications'));
        return data.data.map((v) => Notification.create(v));
    }

    async read(instance: Notification): Notification {
        const url = this.route(`/notifications/${instance.id}/read`);
        const { data } = await axios.patch(url);
        return Notification.create(data.data);
    }
}

export default new NotificationService;