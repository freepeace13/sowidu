/** @flow */

import Order from '~/services/models/order';

export default {
    props: {
        order: {
            type: Order,
            required: true
        },

        readonly: {
            type: Boolean,
            default: false
        },

        orderCopy: {
            type: Order,
            required: true
        }
    }
};