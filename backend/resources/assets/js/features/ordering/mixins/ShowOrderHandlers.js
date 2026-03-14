import { mapMutations } from 'vuex';
import OrderService from '../api';

export default () => ({
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
    }
});