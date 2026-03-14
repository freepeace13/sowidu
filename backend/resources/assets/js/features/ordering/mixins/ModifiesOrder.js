/** @flow */

import { mapState, mapActions, mapMutations } from 'vuex';
import Company from '~/services/models/company';
import Order from '~/services/models/order';
import Delivery from '~/services/models/delivery';
import UsesAuthStore from '~/components/Mixins/UsesAuthStore';
import { isFunction, isNullOrUndefined, isArray, isObject } from '~/support/helpers';
import Events from '~/services/store/websocket/events';
import OrderService from '../api';

export default () => ({
    mixins: [UsesAuthStore()],

    computed: {
        ...mapState({
            order: (state) => state.order.sketch.sheet,
            orderCopy: (state) => state.order.sketch.original,
            errors: (state) => state.order.sketch.errors
        }),

        isReadOnly() {
            if (this.order.contractor && isFunction(this.order.contractor.equals)) {
                return ! this.order.contractor.equals(this.profile);
            }

            if (this.order.customer && isFunction(this.order.customer.billerIs)) {
                return this.order.customer.billerIs(this.profile());
            }

            return true;
        }
    },

    methods: {
        ...mapMutations({
            handleSetSheet: 'order/sketch/SET_SHEET',
            handleResetMembers: 'order/sketch/MEMBERS_UPDATE',
            handleResetTasks: 'order/sketch/TASKS_UPDATE',
            handleInsertTask: 'order/sketch/INSERT_TASK',
            handleResetMedia: 'order/sketch/MEDIA_UPDATE',
            handleResetDeliveries: 'order/sketch/DELIVERIES_UPDATE',
            handleInsertDelivery: 'order/sketch/INSERT_DELIVERY',
            handleUpdateSummary: 'order/sketch/SUMMARY_UPDATE',
            handleResetItems: 'order/sketch/ITEMS_UPDATE',
            handleStateUpdate: 'order/sketch/STATE_UPDATE',
            handleProgressUpdate: 'order/sketch/PROGRESS_UPDATE'
        }),

        registerWebsocketListeners() {
            // $FlowFixMe
            Echo.private(`orders.${this.order.id}`)
                .listen(Events.Order.ProgressUpdated, this.handleProgressUpdate)
                .listen(Events.Order.DeliveriesUpdate, this.handleResetDeliveries)
                .listen(Events.Order.MediaUpdate, this.handleResetMedia)
                .listen(Events.Order.TasksUpdate, this.handleResetTasks)
                .listen(Events.Order.MembersUpdate, this.handleResetMembers)
                .listen(Events.Order.ItemsUpdate, this.handleResetItems)
        },

        async initialize(orderId: number) {
            await this.$store.dispatch('order/sketch/initialize', orderId);
            this.registerWebsocketListeners();
        },

        async handleStateChange({ order, state }: { order: Order, state: OrderStateType }) {
            try {
                await this.$store.dispatch('order/changeState', { order, state });
                this.$events.$emit('alert', 'Order state changed!');
            } catch (error) {
                this.$events.$emit('alert', error.message);
            }
        },

        async handleSavingChanges(instance: Order) {
            try {
                await this.$store.dispatch('order/sketch/update', instance);
                this.$events.$emit('alert', 'Order changes saved!');
            } catch (error) {
                this.$events.$emit('alert', error.message);
            }
        },

        handleConfirm() {
            OrderService.confirm(this.order.id);
        },

        handleCancel() {
            OrderService.cancel(this.order.id);
        }
    },

    beforeDestroy() {
        // $FlowFixMe
        Echo.leave(`orders.${this.order.id}`);
    }
});