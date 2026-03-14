<script setup>
import AddressAutocomplete from '@/Components/AddressAutocomplete.vue'
import { getCurrentInstance } from 'vue'
import useGlobalVariables from '../../../Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'

const app = getCurrentInstance()

const { $route } = useGlobalVariables()

const props = defineProps({
    address: {
        type: Object,
    },
    deliveryTicketId: {
        type: Number,
    },
})

const form = useForm({ delivery_address: props.address })

const update = () => {
    form.transform((data) => ({
        delivery_address: data.delivery_address.id,
    })).put(
        $route('delivery_tickets.delivery-address.update', {
            deliveryTicket: props.deliveryTicketId,
        }),
        {
            only: ['errors', 'flash', 'deliveryTicket'],
            preserveState: true,
            preserveScroll: true,
            onError: (errors) =>
                app.proxy.$root.$emit('flash.validation', errors),
        },
    )
}
</script>

<template>
    <AddressAutocomplete
        v-model="form.delivery_address"
        :loading="form.processing"
        :disabled="form.processing"
        :label="$t('delivery_tickets.form.delivery-address')"
        :error-messages="form.errors.delivery_address"
        :hide-details="!form.errors.deliverery_address"
        @input="update"
    />
</template>
