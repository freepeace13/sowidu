/** @flow */

import { Order, Employee, Task, Delivery, Media, Item } from '~/services/models';
import { callAsync } from '~/support/helpers';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';
import { MemberService } from '~/services/MemberService';
import { TaskService } from '@features/task/api';
import { DeliveryService } from '~/services/DeliveryService';

export const transformers = {
    outgoing: {
        $update: (instance: Order) => ({
            contractor: instance.contractor && instance.contractor.toMorphs()
        })
    },
    incoming: {
        $update: (instance: Order) => ({
            description: instance.description,
            order_date: instance.formattedDates.orderDate,
            delivery_date: instance.formattedDates.deliveryDate,
            items: Item.collection(instance.items).only('id', 'quantity').all(),
            media: Media.collection(instance.media).pluck('id').all(),
            tasks: Task.collection(instance.tasks).pluck('id').all(),
            deliveries: Delivery.collection(instance.deliveries).pluck('id').all(),
            members: instance.members.map((v) => v.toMorphs()),
            state: instance.states.state.current,
            customer_id: instance.customer.id,
        })
    }
}

export class OrderService extends ServiceProvider {
    async all(filters = {}): Promise<Array<Order>> {
        const url = this.route('/client/orders');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url, { params: filters });
            return data.data;
        });

        return result.map((v) => Order.create(v));
    }

    async confirm(orderId: number): Promise<void> {
        return await axios.patch(this.route(`/orders/${orderId}/confirm`));
    }

    async cancel(orderId: number): Promise<void> {
        return await axios.patch(this.route(`/orders/${orderId}/cancel`));
    }

    async allTypes(type: OrderType, state: OrderStateType): Promise<Array<Order>> {
        const url = this.route(`/${type}-orders`);
        const { data } = await axios.get(url, { params: { state }});

        return data.data.map((v) => Order.create(v));
    }

    async retrieve(orderId: number): Promise<Order> {
        const { data } = await axios.get(this.route(`/orders/${orderId}`), {});
        return Order.create(data.data);
    }

    async create(type: OrderType): Promise<Order> {
        const { data } = await axios.post(this.route(`/${type}-orders`));
        return Order.create(data.data);
    }

    async update(order: Order): Promise<Order> {
        const url = this.route(`/${order.type}-orders/${order.id}`);
        const payload: Object = transformers[order.type].$update(order);

        const { data } = await axios.patch(url, payload, {});

        return Order.create(data.data);
    }

    members(orderId: number): MemberService {
        return new MemberService({
            baseURL: `/api/orders/${orderId}`
        });
    }

    tasks(orderId: number): TaskService {
        return new TaskService({
            baseURL: `/api/orders/${orderId}`
        });
    }

    deliveries(orderId: number): DeliveryService {
        return new DeliveryService({
            baseURL: `/api/orders/${orderId}`
        });
    }
}

export default new OrderService;