<template>
    <section id="orders">
        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.orders') }}</v-subheader>

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
                    <v-list light>
                        <v-list-tile>
                            <v-list-tile-title @click="$emit('remove', order)">
                                Remove
                            </v-list-tile-title>
                        </v-list-tile>
                    </v-list>
                </template>
            </OrderCard>
        </v-container>
    </section>
</template>

<script>
import OrderCard from '../OrderCard';
import { Order } from '~/services/models';

export default {
    name: 'FormsOrdersList',

    components: {
        OrderCard,
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