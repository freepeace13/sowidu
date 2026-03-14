<template>
    <section id="orders">
        <v-divider></v-divider>
        <v-subheader class="px-4">Orders</v-subheader>
        <v-container fluid class="py-0">
            <OrderCard
                v-for="order in orders"
                :key="order.id"
                :order="order"
                class="mb-3"
                hide-members
                min-height="90"
            >
                <template slot="menu">
                    <RemoveableCardMenuList @remove="$emit('remove', order)" />
                </template>
            </OrderCard>
        </v-container>
    </section>
</template>

<script>
import RemoveableCardMenuList from './RemoveableCardMenuList';
import OrderCard from '../../Cards/OrderCard';
import { Order } from '~/services/models';

export default {
    name: 'FormsOrdersList',

    components: {
        OrderCard,
        RemoveableCardMenuList
    },

    props: {
        orders: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Order);
            }
        }
    }
}
</script>