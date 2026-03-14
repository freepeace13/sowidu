<template>
    <OrderSectionLayout
        id="order-deliveries"
        :title="$t('labels.deliveries')"
        :hide-actions="!orderCopy.policies.isModifiable()"
    >
        <template slot="actions">
            <v-btn flat color="success" @click="createDelivery('incoming')">
                {{ $t('buttons.new-incoming-delivery') }}
            </v-btn>
            <v-btn flat color="primary" @clickd="createDelivery('outgoing')">
                {{ $t('buttons.new-outgoing-delivery') }}
            </v-btn>
            <v-btn flat @click="browseDeliveries">
                <v-icon>folder</v-icon> &nbsp; {{ $t('buttons.browse') }}
            </v-btn>
        </template>

        <v-layout
            v-for="[date, items] in Object.entries(timeline)"
            :key="date"
            column
        >
            <v-flex xs1>
                <v-subheader>{{ date }}</v-subheader>
                <v-divider></v-divider>
            </v-flex>
            <v-flex>
                <v-layout row wrap>
                    <v-flex
                        lg3 md4 sm6 xs12
                        v-for="delivery in items.all()"
                        :key="delivery.id"
                    >
                        <DeliveryCard
                            :delivery="delivery"
                            :key="delivery.id"
                            :hide-menu="!orderCopy.policies.isModifiable()"
                        >
                            <template slot="menu">
                                <v-list light>
                                    <v-list-tile>
                                        <v-list-tile-title
                                            @click="dispatchRemovedDelivery(delivery)"
                                        >
                                            {{ $t('buttons.remove') }}
                                        </v-list-tile-title>
                                    </v-list-tile>
                                </v-list>
                            </template>
                        </DeliveryCard>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </OrderSectionLayout>
</template>

<script>
import OrderSectionLayout from '../components/OrderSectionLayout';
import DeliveryCard from '~/components/UI/Cards/DeliveryCard';
import DeliveryTableItem from '~/components/dataTables/DeliveryTableItem';
import Delivery from '~/services/models/delivery';
import { showDelivery, showDeliverySelector } from '~/services/events/modal';
import PageMixin from '../mixins/PageMixin';
import ShowOrderHandlers from '../../mixins/ShowOrderHandlers';
import ShowOrderStates from '../../mixins/ShowOrderStates';

export default {
    name: 'EditOrdersDeliveries',

    mixins: [
        ShowOrderHandlers(),
        ShowOrderStates()
    ],

    components: {
        DeliveryTableItem,
        DeliveryCard,
        OrderSectionLayout
    },

    computed: {
        timeline() {
            return Delivery
                .collection(this.order.deliveries)
                .groupBy('deliveryDate')
                .all();
        }
    },

    methods: {
        dispatchRemovedDelivery(delivery) {
            const deliveries = Delivery
                .collection(this.order.deliveries)
                .remove(delivery)
                .all();

            this.handleResetDeliveries(deliveries);
        },

        dispatchInsertedDelivery(delivery) {
            const deliveries = Delivery
                .collection(this.order.deliveries)
                .insert(delivery)
                .all();

            this.handleResetDeliveries(deliveries);
        },

        createDelivery(type) {
            showDelivery({ type,
                onSuccess: (response) => {
                    this.dispatchInsertedDelivery(response.value);
                    response.close();
                }
            });
        },

        browseDeliveries() {
            showDeliverySelector({
                selected: this.order.deliveries,
                onSelect: (response) => {
                    this.dispatchInsertedDelivery(response.value);
                    response.close();
                }
            });
        }
    }
}
</script>
