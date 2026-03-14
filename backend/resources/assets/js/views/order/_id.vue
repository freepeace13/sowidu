<template>
    <v-layout row wrap>
        <v-flex xs2>
            <SidebarMenu
                :saving="$wait.is('order/sketch/update')"
                :order="order"
                :order-copy="orderCopy"
                @save="handleSavingChanges(order)"
                @cancel="handleCancel(order)"
                @confirm="handleConfirm(order)"
                @update-members="handleResetMembers"
                @update-state="handleStateUpdate"
            />
        </v-flex>
        <v-flex xs10>
            <StateProgress :order="orderCopy" />

            <v-container fluid class="py-0 px-0">
                <router-view
                    :readonly="isReadOnly"
                    :errors="errors"
                    :order="order"
                    :order-copy="orderCopy"
                    @update-tasks="handleResetTasks"
                    @update-media="handleResetMedia"
                    @update-deliveries="handleResetDeliveries"
                    @update-summary="handleUpdateSummary"
                ></router-view>
            </v-container>
        </v-flex>
    </v-layout>
</template>

<script>
import StateProgress from './components/StateProgress';
import SidebarMenu from './components/SidebarMenu';
import { ModifiesOrder } from '~/components/Mixins';

export default {
    mixins: [
        ModifiesOrder(),
    ],

    components: {
        StateProgress,
        SidebarMenu
    },

    mounted() {
        this.initialize(this.$route.params.id);
    }
}
</script>
