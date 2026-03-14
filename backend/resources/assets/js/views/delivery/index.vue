<template>
    <v-layout row>
        <v-flex xs2>
            <Sidebar/>
        </v-flex>
        <v-flex xs10>
            <v-card color="grey darken-4 mb-3">
                <NavigationTabs/>
                <v-divider></v-divider>
                <!-- <FilterToolbar></FilterToolbar> -->
            </v-card>

            <v-wait
                for="delivery/all"
                transition="fade"
                class="layout fill-height justify-start align-start"
            >
                <SuspensionSpinner slot="waiting" />
                
                <v-layout
                    v-for="[date, items] of Object.entries(deliveriesTimeline)"
                    :key="date"
                    column
                >
                    <v-flex xs1>
                        <v-subheader>{{ date }}</v-subheader>
                    </v-flex>
                    <v-flex>
                        <v-layout row wrap>
                            <v-flex
                                lg3 md4 sm6 xs12
                                v-for="delivery in items.all()"
                                :key="`${date}_${delivery.id}`"
                            >
                                <DeliveryCard
                                    :delivery="delivery"
                                    @clickmenu-edit="viewDelivery(delivery.id)"
                                    @click:title="viewDelivery(delivery.id)"
                                />
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-wait>

            <section>
                <v-btn
                    color="primary"
                    fixed fab bottom right
                    @click="createDelivery()"
                >
                    <v-icon>add</v-icon>
                </v-btn>
            </section>
        </v-flex>
    </v-layout>
</template>

<script>
import SuspensionSpinner from '@common/components/SuspensionSpinner';
import Sidebar from './components/Sidebar';
import FilterToolbar from './components/FilterToolbar';
import NavigationTabs from './components/NavigationTabs';
import Delivery from '~/services/models/delivery';
import { showDelivery } from '~/services/events/modal';
import { DELIVERY_TYPES } from '~/support/constants';
import UsesDeliveryStore from '~/components/Mixins/UsesDeliveryStore';
import DeliveryCard from '~/components/UI/Cards/DeliveryCard';

export default {
    name: 'DeliveryListView',

    mixins: [UsesDeliveryStore()],

    components: {
        DeliveryCard,
        Sidebar,
        FilterToolbar,
        NavigationTabs,
        SuspensionSpinner,
    },

    computed: {
        typeQuery() {
            const types = ['incoming', 'outgoing'];

            return types.includes(this.$route.query.type)
                ? this.$route.query.type
                : 'incoming';
        },

        filteredTypeDeliveries() {
            return this.deliveries.filter((v) => v.type === this.typeQuery);
        },

        deliveriesTimeline() {
            return Delivery.collection(this.filteredTypeDeliveries)
                .groupBy('deliveryDate')
                .all();
        }
    },

    methods: {
        viewDelivery(deliveryId) {
            this.$router.push({
                name: 'deliveries',
                params: { id: deliveryId },
                query: this.$route.query
            }).catch(() => {
                this.$router.push({ name: 'deliveries' });
            });
        },

        createDelivery() {
            showDelivery({
                deliveryId: undefined,
                type: this.typeQuery,
            });
        }
    },

    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                this.$nextTick(() => {
                    if (typeof to.params.id !== 'undefined') {
                        showDelivery({
                            deliveryId: to.params.id,
                            type: this.typeQuery,
                            afterClose: () => {
                                this.$router.push({
                                    name: 'deliveries',
                                    query: this.$route.query
                                });
                            }
                        });
                    }
                });
            }
        }
    }
}
</script>
