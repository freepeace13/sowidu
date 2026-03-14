<script setup>
import { getCurrentInstance } from 'vue'
import useGlobalVariables from '../../../Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import AddressbookAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressbookAutocomplete.vue'

const app = getCurrentInstance()

const { $route } = useGlobalVariables()

const props = defineProps({
    deliverer: {
        type: [Array, Object],
    },
    deliveryTicketId: {
        type: Number,
    },
})

const form = useForm({
    deliverer: Array.isArray(props.deliverer)
        ? { name: '', photo: '' }
        : props.deliverer,
})

const update = () => {
    form.transform((data) => ({
        deliverer: data.deliverer.id,
    })).put(
        $route('delivery_tickets.delivery-address.update', {
            deliveryTicket: props.deliveryTicketId,
        }),
        {
            only: ['errors', 'flash', 'order', 'deliveryTicket'],
            preserveState: true,
            preserveScroll: true,
            onError: (errors) =>
                app.proxy.$root.$emit('flash.validation', errors),
        },
    )
}
</script>

<template>
    <AddressbookAutocomplete
        v-model="form.deliverer"
        :loading="form.processing"
        :disabled="form.processing"
        :label="$t('delivery_tickets.form.deliverer')"
        :error-messages="form.errors.deliverer"
        :hide-details="!form.errors.deliverer"
        @input="update"
    />
</template>
