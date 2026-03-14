<template>
    <v-card class="tw-relative">
        <v-img
            :src="client.photo"
            height="200px"
            contain
        />
        <ClientCompanyCard
            v-if="client?.is_company"
            :client="client"
            :delivery-address="deliveryAddress"
            :is-show-edit-delivery-address="
                $page.props.viewer?.can_update_order ?? false
            "
            @click:edit-delivery-address="
                $refs.editOrderDeliveryAddress.show($page.props.order)
            "
        />
        <ClientPersonCard
            v-else
            :client="client"
            :delivery-address="deliveryAddress"
            :is-show-edit-delivery-address="
                $page.props.viewer?.can_update_order ?? false
            "
            @click:edit-delivery-address="
                $refs.editOrderDeliveryAddress.show($page.props.order)
            "
        />
        <v-btn
            v-if="$page.props.viewer?.can_update_client"
            icon
            class="!tw-absolute !tw-top-0 !tw-right-0 tw-cursor-pointer"
            @click="$refs.editOrderClientForm.show($page.props.order)"
        >
            <v-icon>more_horiz</v-icon>
        </v-btn>
        <EditOrderClientForm ref="editOrderClientForm" />
        <EditOrderDeliveryAddress
            v-if="$page.props.viewer?.can_update_order"
            ref="editOrderDeliveryAddress"
        />
    </v-card>
</template>
<script>
import { getBaseAddress, getDistinctAddress } from '@/Composables/useAddress'
import ClientPersonCard from './Client/ClientPersonCard.vue'
import ClientCompanyCard from './Client/ClientCompanyCard.vue'
import EditOrderClientForm from './EditOrderClientForm.vue'
import EditOrderDeliveryAddress from './EditOrderDeliveryAddress.vue'

export default {
    components: {
        ClientPersonCard,
        ClientCompanyCard,
        EditOrderClientForm,
        EditOrderDeliveryAddress,
    },
    filters: {
        baseAddress: (value) => getBaseAddress(value),
        distinctAddress: (value) => getDistinctAddress(value),
    },

    props: {
        client: {
            required: true,
            type: Object,
        },
        deliveryAddress: {
            required: true,
            type: Object,
        },
    },
}
</script>
