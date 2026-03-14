<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-container grid-list-lg fluid>
            <OrderCard
                v-for="order in selections"
                :key="order.id"
                :order="order"
                hide-members
                hide-menu
                min-height="100"
                @click.native="select(order)"
            />
        </v-container>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import UsesOrderStore from '../../mixins/UsesOrderStore';
import OrderCard from '../../components/OrderCard';
import { Order } from '~/services/models';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'OrderSelectorModal',

    mixins: [UsesOrderStore(), DispatchWhenTokenChanges('order/all')],

    components: { OrderCard },

    props: {
        selected: {
            type: Array,
            default: () => ([]),
        },

        onSelect: {
            type: Function,
            required: true
        }
    },

    computed: {
        selections() {
            const selected = Order.collection(this.selected);
            return this.orders.filter((v) => ! selected.includes(v));
        }
    },

    methods: {
        select(order) {
            this.onSelect(new Response(this, order));
        },
    }
}
</script>