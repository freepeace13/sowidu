<template>
    <div style="width:100%">
        <v-layout
            v-for="[date, items] of Object.entries(timeline)"
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
    </div>
</template>

<script>
import Delivery from '~/services/models/delivery';
import UsesDeliveryStore from '../../mixins/UsesDeliveryStore';
import DeliveryCard from '../../components/DeliveryCard';

export default {
    name: 'IndexOutgoingView',

    mixins: [UsesDeliveryStore()],

    inject: ['viewDelivery'],

    components: {  DeliveryCard },

    computed: {
        collection() {
            return this.deliveries.filter((item) => item.type === 'outgoing');
        },

        timeline() {
            return Delivery
                .collection(this.collection)
                .groupBy('deliveryDate')
                .all();
        }
    }
}
</script>