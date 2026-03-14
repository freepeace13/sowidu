<template>
    <router-view></router-view>
</template>

<script>
import SuspensionSpinner from '@common/components/SuspensionSpinner';
import Sidebar from './components/Sidebar';
import FilterToolbar from './components/FilterToolbar';
import NavigationTabs from './components/NavigationTabs';
import Delivery from '~/services/models/delivery';
import { showDelivery } from '~/services/events/modal';
import { DELIVERY_TYPES } from '~/support/constants';
import DeliveryCard from '~/components/UI/Cards/DeliveryCard';
import { createResource } from 'vue-async-manager';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'DeliveriesIndexView',

    mixins: [
        DispatchWhenTokenChanges('delivery/all')
    ],

    components: {
        DeliveryCard,
        Sidebar,
        FilterToolbar,
        NavigationTabs,
        SuspensionSpinner,
    },

    provide() {
        return {
            viewDelivery(deliveryId) {
                window.location.hash = `#preview-${deliveryId}`;
            }
        }
    },

    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                let deliveryId;

                if (to.hash) {
                    const pieces = to.hash.split('-');
                    if (pieces[0] === '#preview' && pieces[1]) {
                        deliveryId = pieces[1];
                    }
                }

                if (!deliveryId) return;

                this.$nextTick(() => {
                    showDelivery({
                        deliveryId,
                        type: to.name.split('.')[1],
                        afterClose: () => {
                            window.location.hash = "";
                        }
                    });
                });
            }
        }
    }
}
</script>
