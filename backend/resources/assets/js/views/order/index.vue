<template>
    <RootView title="Orders" icon="reorder">
        <v-layout row>
            <v-flex xs2>
                <ListViewSidebar @state="filterBy('state', $event)" />
            </v-flex>
            <v-flex xs10>
                <v-tabs
                    color="grey darken-4"
                    class="mb-2"
                    grow
                    slider-color="white"
                >
                    <v-tab exact @click="filterBy('type', 'all')">
                        All
                    </v-tab>
                    <v-tab exact @click="filterBy('type', 'incoming')">
                        Incoming
                    </v-tab>
                    <v-tab exact @click="filterBy('type', 'outgoing')">
                        Outgoing
                    </v-tab>
                </v-tabs>

                <v-layout row wrap>
                    <v-flex xs4 v-for="order in filteredLists" :key="order.id">
                        <OrderCard
                            :order="order"
                            @clickmenu-edit="editOrder(order.id)"
                            @click:title="editOrder(order.id)"
                        />
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>

        <v-speed-dial
            :fixed="true"
            direction="left"
            :right="true"
            :bottom="true"
            transition="scale"
        >
            <v-btn color="primary" slot="activator" dark fab>
                <v-icon>add</v-icon>
            </v-btn>

            <v-btn color="grey darken-4" @click="$orders.create('incoming')">
                New Incoming Order
            </v-btn>
            <v-btn color="grey darken-4" @click="$orders.create('outgoing')">
                New Outgoing Order
            </v-btn>
        </v-speed-dial>
    </RootView>
</template>

<script>
import Order from '~/services/models/order';
import { UsesOrderStore } from '~/components/Mixins';
import OrderCard from '~/components/UI/Cards/OrderCard';
import ListViewSidebar from './components/ListViewSidebar';

const mapQueryStates = (state) => (({
    all: ['pending', 'final'],
    drafts: ['preparation'],
    pending: ['completed'],
    history: ['done', 'cancelled']
})[state]);

export default {
    name: 'OrderListView',

    mixins: [UsesOrderStore()],

    components: {
        OrderCard,
        ListViewSidebar
    },

    computed: {
        filteredLists() {
            const { type, state } = this.$route.query;
            const queryStates = mapQueryStates(state || 'all');

            return Order.collection(this.orders)
                .when(type !== 'all', (query) => query.where('type', type))
                .filter((value, key) => {
                    return value.policies.states('state').isOneOf(...queryStates);
                })
                .all();
        }
    },

    methods: {
        editOrder(id) {
            this.$router.replace({ name: 'orders.edit', params: { id }});
        },

        filterBy(key, value) {
            this.$router.replace({ name: 'orders', query: {
                ...this.$route.query,
                [key]: value
            }});
        }
    }
}
</script>
