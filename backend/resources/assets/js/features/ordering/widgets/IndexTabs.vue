<template>
    <v-toolbar height="64" card prominent>
        <v-toolbar-items>
            <v-tabs
                height="64"
                slider-color="grey"
                color="transparent"
                active-class="font-weight-bold"
            >
                <v-tab exact
                    v-can="allowViewIncomingOrders"
                    :to="{ name: 'orders.incoming' }"
                >
                    {{ $t('headings.incoming-orders') }}
                </v-tab>

                <v-tab exact
                    v-can="allowViewOutgoingOrders"
                    :to="{ name: 'orders.outgoing' }"
                >
                    {{ $t('headings.outgoing-orders') }}
                </v-tab>
            </v-tabs>
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <v-toolbar-items>
            <FilterByStatus />

            <ToolbarCreateMenu
                v-can:any="allowCreateOrder"
                @click:outgoing="$orders.create('outgoing')"
                @click:incoming="$orders.create('incoming')"
            />
        </v-toolbar-items>
    </v-toolbar>
</template>

<script>
import FilterByStatus from './StatusFilter';
import ToolbarCreateMenu from '../components/ToolbarCreateMenu';
import UsesOrderStore from '../mixins/UsesOrderStore';
import * as Enums from '../enums';

export default {
    name: 'IndexTabs',

    mixins: [UsesOrderStore()],

    components: {
        ToolbarCreateMenu,
        FilterByStatus
    },

    computed: {
        allowCreateOrder() {
            return [
                Enums.PERMISSIONS.CREATE_OUTGOING_ORDER,
                Enums.PERMISSIONS.CREATE_INCOMING_ORDER
            ];
        },

        allowViewIncomingOrders() {
            return Enums.PERMISSIONS.VIEW_INCOMING_ORDERS;
        },

        allowViewOutgoingOrders() {
            return Enums.PERMISSIONS.VIEW_OUTGOING_ORDERS;
        }
    }
}
</script>