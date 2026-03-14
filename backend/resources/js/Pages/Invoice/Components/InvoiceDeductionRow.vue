<script setup>
import AppAvatar from '@/Components/AppAvatar.vue'
import { computed, toRefs } from 'vue'

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
})

defineEmits(['click:row'])

const { invoice } = toRefs(props)

const client = computed(() => invoice.value.client)
const order = computed(() => invoice.value.order)
const deliveryAddress = computed(() => invoice.value.delivery_address)
const isPaid = computed(() => invoice.value.is_paid)
</script>
<template>
    <tr
        :class="[
            {
                'tw-bg-green-50': isPaid,
            },
            'tw-align-start',
        ]"
        @click="() => $emit('click:row')"
    >
        <slot name="checkbox" />
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
        <!-- <td class="tw-text-right tw-whitespace-nowrap">
            {{
                invoice.visible
                    ? invoice.total_after_deduction
                    : invoice.total_price
            }}
            {{ invoice.biller?.currency?.symbol }}
        </td> -->

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
    </tr>
</template>
