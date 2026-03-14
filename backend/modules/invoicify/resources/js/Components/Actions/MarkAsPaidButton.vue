<script setup>
import { toRef, computed } from 'vue'
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

/**
 * @typedef {Object} Props
 * @property {import('../../types/invoice').Invoice} invoice - The invoice object
 */
const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },

    invoice: {
        type: Object,
        required: true,
        validator: (value) => {
            return (
                value &&
                typeof value.id === 'number' &&
                typeof value.is_paid === 'boolean'
            )
        },
    },
})

const form = useForm({})
const isTile = toRef(props, 'tile')
const { $t, $confirm, $route } = useGlobalVariables()

const isDisabled = computed(() => {
    return (
        form.processing ||
        props.invoice.is_paid ||
        props.invoice.status.is_draft
    )
})

const icon = computed(() => {
    return props.invoice.is_paid ? 'done_outline' : 'check_circle'
})

const label = computed(() => {
    return props.invoice.is_paid
        ? $t('invoices.labels.paid')
        : $t('invoices.buttons.mark-as-paid')
})

function markAsPaid() {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.message.confirm_marking_as_paid'),
        type: 'warning',
        confirm: () => {
            form.post(
                $route('invoicify.mark_as_paid', { invoice: props.invoice.id }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'flash'],
                    onError: console.error,
                },
            )
        },
    })
}
</script>

<template>
    <div>
        <v-list-tile
            v-if="isTile"
            avatar
            :disabled="isDisabled"
            @click="markAsPaid"
        >
            <v-list-tile-avatar>
                <v-icon color="success">{{ icon }}</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>{{ label }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
        <v-btn
            v-else
            depressed
            :disabled="isDisabled"
            :loading="form.processing"
            color="info"
            @click="markAsPaid"
        >
            <v-icon left>{{ icon }}</v-icon> {{ label }}
        </v-btn>
    </div>
</template>
