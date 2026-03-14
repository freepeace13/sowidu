/** @flow */

import { ModelCollection as Collection } from '~/support/wrappers';
import { Task, Delivery, Order } from '~/services/models';

export function mutateTasks(
    state: Object,
    orderId: number,
    mutator: (v: Collection<Task>) => Array<Task>
): Order {

    const order = Order
        .collection(state.orders)
        .find(orderId);

    order.tasks = mutator(
        Task.collection(order.tasks)
    );

    return order;
}

export function mutateDeliveries(
    state: Object,
    orderId: number,
    mutator: (v: Collection<Delivery>) => Array<Delivery>
): Order {

    const order = Order
        .collection(state.orders)
        .find(orderId);

    order.deliveries = mutator(
        Delivery.collection(order.deliveries)
    );

   return order;
}