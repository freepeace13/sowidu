/** @flow */

import { showDeliverySelector } from '~/services/events/modal';
import { Delivery } from '~/services/models';

export default (propName: string) => ({
    methods: {
        removeDelivery(delivery: Delivery) {
            this[propName].deliveries = Delivery
                .collection(this[propName].deliveries)
                .remove(delivery)
                .all();
        },

        browseDeliveries() {
            showDeliverySelector({
                selected: this[propName].deliveries,
                onSelect: (response) => {
                    this[propName].deliveries = Delivery
                        .collection(this[propName].deliveries)
                        .insert(response.value)
                        .all();

                    response.close();
                }
            }, true);
        }
    }
});