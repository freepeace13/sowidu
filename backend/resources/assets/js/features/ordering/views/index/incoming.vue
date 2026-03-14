<template>
    <v-layout row wrap>
        <v-flex xs4 v-for="order in incomingOrders" :key="order.id">
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
import { createResource } from 'vue-async-manager';
import api from '../../api';
import CallWhenTokenChanged from '@common/mixins/CallWhenTokenChanged';

export default {
    name: 'IndexIncomingView',

    data: () => ({
        incomingOrders: []
    }),

    mixins: [
        UsesOrderStore(),
        CallWhenTokenChanged(function () {
            this.$rm.read(this.$route.query.state || []);
        })
    ],

    components: {
        OrderCard,
    },

    computed: {
        collection() {
            const stateQuery = this.$route.query.state || FILTER_TYPES.STATE.DEFAULT;

            return this.incoming.filter((order) => {
                return order.policies.states('state').is(stateQuery)
            });
        }
    },

    created() {
        this.$rm = createResource(async (filters = []) => {
            console.log(filters);
            this.incomingOrders = await api.all({
                type: 'incoming',
                state: filters
            });
        });
    },

    watch: {
        $route(to) {
            this.$rm.read(to.query.state || []);
        }
    }
}
</script>