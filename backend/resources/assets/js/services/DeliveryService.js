/** @flow */

import axios from 'axios';
import { callAsync } from '~/support/helpers';
import { TaskService } from './TaskService';
import { OrderService } from './OrderService';
import { MediaService } from './MediaService';
import ServiceProvider from '@libs/ServiceProvider';
import { Delivery, Task, Order, Media, Item, Employee } from '~/services/models';

export function transformPayload(delivery: Delivery) {
    const items = Item.collection(delivery.items);
    const media = Media.collection(delivery.media);
    const orders = Order.collection(delivery.orders);
    const tasks = Task.collection(delivery.tasks);

    const payload: Object = {
        title: delivery.title,
        remarks: delivery.remarks,
        items: items.only('id', 'quantity').all(),
        orders: orders.pluck('id').all(),
        media: media.pluck('id').all(),
        tasks: tasks.pluck('id').all(),
        members: delivery.members.map((v) => v.toMorphs())
    }

    if (delivery.type === 'incoming') {
        payload.customer_id = delivery.customer && delivery.customer.id;
    } else if (delivery.type === 'outgoing') {
        payload.contractor = delivery.contractor && delivery.contractor.toMorphs()
    }

    return payload;
}

export class DeliveryService extends ServiceProvider {
    async all(): Promise<Array<Delivery>> {
        const url = this.route('/deliveries');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url, {});
            return data.data;
        });

        return result.map((v) => Delivery.create(v));
    }

    async allTypes(type: DeliveryType): Promise<Array<Delivery>> {
        const { data } = await axios.get(this.route(`/deliveries/${type}`), {});
        return data.data.map((v) => Delivery.create(v));
    }

    async retrieve(deliveryId: number): Promise<Delivery> {
        const { data } = await axios.get(this.route(`/deliveries/${deliveryId}`));
        return Delivery.create(data.data);
    }

    async create(delivery: Delivery): Promise<Delivery> {
        const url = this.route(`/deliveries/${delivery.type}`);

        const payload: Object = transformPayload(delivery);
        const { data } = await axios.post(url, payload);

        return Delivery.create(data.data);
    }

    async update(delivery: Delivery): Promise<Delivery> {
        const url = this.route(`/deliveries/${delivery.type}/${delivery.id}`);

        const payload: Object = transformPayload(delivery);
        const { data } = await axios.patch(url, payload);

        return Delivery.create(data.data);
    }

    tasks(deliveryId: number): TaskService {
        return new TaskService({
            baseURL: `/api/deliveries/${deliveryId}`
        });
    }

    orders(deliveryId: number): OrderService {
        return new OrderService({
            baseURL: `/api/deliveries/${deliveryId}`
        });
    }

    media(deliveryId: number): MediaService {
        return new MediaService({
            baseURL: `/api/deliveries/${deliveryId}`
        });
    }
}

export default new DeliveryService;