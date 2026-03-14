<template>
    <v-toolbar height="64" card>
        <v-toolbar-items>
            <v-tabs
                height="64"
                slider-color="grey"
                color="transparent"
                active-class="font-weight-bold"
            >
                <v-tab exact :to="{ name: 'deliveries.incoming' }">
                    {{ $t('headings.incoming-deliveries') }}
                </v-tab>

                <v-tab exact :to="{ name: 'deliveries.outgoing' }">
                    {{ $t('headings.outgoing-deliveries') }}
                </v-tab>
            </v-tabs>
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <v-btn icon @click="createDelivery" v-can:any="allowCreateDelivery">
            <v-icon>add</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
import * as DeliveryEnums from '../enums';
import { showDelivery } from '~/services/events/modal';

export default {
    name: 'IndexTabs',

    computed: {
        allowCreateDelivery() {
            return [
                DeliveryEnums.PERMISSIONS.CREATE_INCOMING_DELIVERY,
                DeliveryEnums.PERMISSIONS.CREATE_OUTGOING_DELIVERY,
            ];
        }
    },

    methods: {
        createDelivery() {
            showDelivery({
                deliveryId: undefined,
                type: this.$route.name.split('.')[1],
            });
        }
    }
}
</script>