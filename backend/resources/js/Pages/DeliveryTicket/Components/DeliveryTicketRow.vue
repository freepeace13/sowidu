<script setup>
//import AppAvatar from '@/Components/AppAvatar.vue'
import { useDateFormat } from '@/Composables/useDayJs'
import { nullSafe } from '@/Composables/useFilters'
import { computed } from 'vue'
import DeliveryTicketAddress from './DeliveryTicketAddress.vue'
import DeliveryTicketDeliverer from './DeliveryTicketDeliverer.vue'

const props = defineProps({
    deliveryTicket: {
        required: true,
        type: Object,
    },
    deletable: {
        required: false,
        type: Boolean,
        default: true,
    },
    editable: {
        required: false,
        type: Boolean,
        default: true,
    },
})

const deliverer = computed(() => props.deliveryTicket.deliverer)
const order = computed(() => props.deliveryTicket?.order)
const deliveryAddress = computed(() => props.deliveryTicket?.delivery_address)

const noDeliveryAddress = computed(() => {
    return (
        Object.keys(deliveryAddress.value).filter((key) => {
            return deliveryAddress.value[key]
        }).length < 2
    )
})
</script>
<template>
    <tr>
        <td>
            <a
                class="info--text hover:tw-underline tw-font-semibold"
                @click="$emit('click:show-details', deliveryTicket.id)"
            >
                {{ deliveryTicket?.internal_id ?? deliveryTicket.id }}
            </a>
        </td>
        <td>
            {{ nullSafe(deliveryTicket?.external_id) }}
        </td>
        <td class="">
            <div class="tw-flex tw-items-center tw-gap-x-2 tw-relative">
                <div
                    v-if="Array.isArray(deliverer)"
                    class="tw-absolute tw-right-4 tw-top-1"
                >
                    <v-icon color="red">info</v-icon>
                </div>
                <DeliveryTicketDeliverer
                    :deliverer="deliverer"
                    :delivery-ticket-id="deliveryTicket.id"
                />
            </div>
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
        <td class="tw-table-cell !tw-h-auto md:tw-h-12 px-2 tw-relative">
            <div class="tw-flex tw-items-start tw-justify-between">
                <div class="py-1 tw-flex-1">
                    <DeliveryTicketAddress
                        :address="deliveryAddress"
                        :delivery-ticket-id="deliveryTicket.id"
                    />
                </div>
                <div
                    v-if="noDeliveryAddress"
                    class="tw-absolute tw-right-6 tw-top-2"
                >
                    <v-icon color="red">info</v-icon>
                </div>
            </div>
        </td>
        <td>
            {{ useDateFormat(deliveryTicket.delivery_date, 'DD.MM.YYYY') }}
        </td>
        <td class="tw-text-center">
            {{ deliveryTicket.total_purchasing_price_formatted }}
        </td>
        <td class="tw-text-center">
            {{ deliveryTicket.total_selling_price_formatted }}
        </td>
        <td class="!tw-text-right">
            <div class="tw-flex">
                <v-btn
                    v-if="editable"
                    small
                    color="info"
                    flat
                    class="tw-my-0"
                    icon
                    @click="$emit('click:edit', deliveryTicket)"
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
                <v-btn
                    v-if="deletable"
                    small
                    flat
                    class="tw-my-0"
                    icon
                    color="error"
                    @click="$emit('click:delete', deliveryTicket)"
                >
                    <v-icon small>delete</v-icon>
                </v-btn>
            </div>
        </td>
        <slot name="additional" />
    </tr>
</template>
