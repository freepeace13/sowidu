<script setup>
import AppAvatar from '@/Components/AppAvatar.vue'
import { computed, toRefs } from 'vue'

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
    canBeUpdated: {
        required: false,
        type: Boolean,
        default: false,
    },
    canBeDeleted: {
        required: false,
        type: Boolean,
        default: false,
    },
    selectable: {
        required: false,
        type: Boolean,
        default: false,
    },
    selected: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const { invoice } = toRefs(props)

const client = computed(() => invoice.value.client)
const order = computed(() => invoice.value.order)
const deliveryAddress = computed(() => invoice.value.delivery_address)
const isPaid = computed(() => invoice.value.is_paid)
</script>
<template>
    <tr
        :active="selected"
        :class="[
            {
                'tw-bg-green-50': isPaid,
            },
            'tw-align-start',
        ]"
    >
        <slot name="checkbox" />
        <td>{{ invoice?.id }}</td>
        <td>{{ invoice?.type.name }}</td>

        <td>
            <a
                class="info--text hover:tw-underline tw-font-semibold"
                @click="$emit('click:show-details', invoice.id)"
            >
                {{ invoice?.external_id ?? invoice?.internal_id ?? invoice.id }}
            </a>
        </td>
        <td class="">
            <div class="tw-flex tw-items-center tw-gap-x-2">
                <AppAvatar :avatar="client.photo" />
                {{ client.name }}
            </div>
        </td>
        <td class="tw-text-right tw-whitespace-nowrap">
            {{ invoice?.total_amount_formatted }}
        </td>

        <td>
            <a
                class="info--text hover:tw-underline tw-font-semibold"
                :href="
                    $route('orders.show', {
                        order: order?.id,
                    })
                "
                target="_blank"
            >
                {{ order?.order_number ?? '--' }}
            </a>
        </td>
        <td>
            <v-chip
                :color="invoice.status.color"
                label
            >
                {{ invoice?.status.label }}
            </v-chip>
        </td>
        <td class="tw-table-cell !tw-h-auto md:tw-h-12 px-2">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div class="py-1">
                    {{ deliveryAddress.full }}
                </div>
            </div>
        </td>
        <td>
            {{ invoice.delivery_date | formatDate('DD.MM.YYYY') }}
        </td>
        <td class="!tw-text-right tw-flex tw-items-center">
            <div class="tw-flex tw-items-center">
                <a
                    target="_blank"
                    :href="
                        $route('invoices.show', {
                            invoice: invoice.id,
                        })
                    "
                >
                    <v-btn
                        v-tooltip="`${$t('invoices.buttons.preview')}`"
                        small
                        color="blue-info"
                        flat
                        class="!tw-my-0 !tw-mx-0 white--text"
                        icon
                    >
                        <v-icon small>visibility</v-icon>
                    </v-btn>
                </a>

                <v-btn
                    v-if="invoice.can_add_payments"
                    v-tooltip="`${$t('invoices.buttons.add_payment')}`"
                    small
                    color="blue-info"
                    flat
                    class="!tw-my-0 !tw-mx-0 white--text"
                    icon
                    @click="$emit('click:add-payment', invoice)"
                >
                    <v-icon>payments</v-icon>
                </v-btn>

                <v-btn
                    v-if="canBeUpdated"
                    small
                    color="info"
                    flat
                    class="!tw-my-0 !tw-mx-0"
                    icon
                    @click="$emit('click:edit', invoice)"
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
                <v-btn
                    v-if="canBeDeleted"
                    small
                    flat
                    class="!tw-my-0 !tw-mx-0"
                    icon
                    color="error"
                    @click="$emit('click:delete', invoice)"
                >
                    <v-icon small>delete</v-icon>
                </v-btn>
            </div>
        </td>
    </tr>
</template>
