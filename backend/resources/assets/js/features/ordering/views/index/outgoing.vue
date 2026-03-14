<template>
    <v-layout row wrap>
        <v-flex xs4 v-for="order in collection" :key="order.id">
            <OrderCard
                :order="order"
                @clickmenu-edit="$router.push({
                    name: 'orders.show',
                    params: { id: order.id }
                })"
                @click:title="$router.push({
                    name: 'orders.show',
                    params: { id: order.id }
                })"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import UsesOrderStore from '../../mixins/UsesOrderStore';
import OrderCard from '../../components/OrderCard';
import { FILTER_TYPES } from '../../enums';

export default {
    name: 'IndexOutgoingView',

    mixins: [UsesOrderStore()],

    components: {
        OrderCard
    },

    computed: {
        collection() {
            const stateQuery = this.$route.query.state || FILTER_TYPES.STATE.DEFAULT;

            return this.outgoing.filter((order) => {
                return order.policies.states('state').is(stateQuery)
            });
        }
    }
}
</script>