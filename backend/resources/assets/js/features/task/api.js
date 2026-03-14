/** @flow */

import type { ServicePayload } from 'task-service-payload';
import type { APIPayload } from 'task-api-payload';
import { Task, Order, Delivery, Employee, Media, Comment } from '~/services/models';
import { callAsync } from '~/support/helpers';
import { CommentService } from '~/services/CommentService';
import { MemberService } from '~/services/MemberService';
import { DeliveryService } from '~/services/DeliveryService';
import { OrderService } from '~/services/OrderService';
import ServiceProvider from '@libs/ServiceProvider';
import axios from 'axios';

export function _toPayload(payload: Task): APIPayload {
    return {
        title: payload.title,
        description: payload.description,
        state: payload.states.state.current,
        members: payload.members.map((v) => v.toMorphs()),
        deliveries: payload.deliveries.map((v) => (v instanceof Delivery) ? v.id : v),
        orders: payload.orders.map((v) => (v instanceof Order) ? v.id : v),
        media: payload.media.map((v) => (v instanceof Media) ? v.id : v)
    }
};

export class TaskService extends ServiceProvider {
    async all(): Promise<Array<Task>> {
        const url = this.route('/tasks');

        const result = await callAsync(async () => {
            const { data } = await axios.get(url, {});
            return data.data
        });

        return result.map((task) => Task.create(task));
    }

    async retrieve(taskId: number): Promise<Task> {
        const url = this.route(`/tasks/${taskId}`);
        const { data } = await axios.get(url, {});
        return Task.create(data.data);
    }

    async update(instance: Task): Promise<Task> {
        const url = this.route(`/tasks/${instance.id}`);
        const { data } = await axios.patch(url, _toPayload(instance), {});
        return Task.create(data.data);
    }

    async create(instance: Task): Promise<Task> {
        const url = this.route('/tasks');
        const { data } = await axios.post(url, _toPayload(instance), {});
        return Task.create(data.data);
    }

    orders(taskId: number): OrderService {
        return new OrderService({
            baseURL: `/api/tasks/${taskId}`
        });
    }

    deliveries(taskId: number): DeliveryService {
        return new DeliveryService({
            baseURL: `/api/tasks/${taskId}`
        });
    }

    members(taskId: number): MemberService {
        return new MemberService({
            baseURL: `/api/tasks/${taskId}`
        });
    }

    comments(taskId: number): CommentService {
        return new CommentService({
            baseURL: `/api/tasks/${taskId}`
        });
    }
}

export default new TaskService();