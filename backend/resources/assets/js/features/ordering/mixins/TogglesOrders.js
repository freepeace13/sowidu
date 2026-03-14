/** @flow */

import { showOrderSelector } from '~/services/events/modal';
import { Order } from '~/services/models';

export default (propName: string) => ({
    methods: {
        removeOrder(order: Order) {
            this[propName].orders = Order
                .collection(this[propName].orders)
                .remove(order)
                .all();
        },

        browseOrders() {
            showOrderSelector({
                selected: this[propName].orders,
                onSelect: (response) => {
                    this[propName].orders = Order
                        .collection(this[propName].orders)
                        .insert(response.value)
                        .all();

                    response.close();
                }
            }, true);
        }
    }
});