<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import JumboUploadButton from '../Files/Components/JumboUploadButton.vue'
import DeliveryTicketForm from '@/Pages/DeliveryTicket/Components/DeliveryTicketForm.vue'
import { ref } from 'vue'
import { deliveryTicketIsIncoming } from '@/Modules/OrderConstants'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
import { authCan } from '@/Composables/useAuth'
import DeliveryTicketImportForm from '@/Pages/DeliveryTicket/Components/DeliveryTicketImportForm.vue'

defineProps({
    order: {
        required: true,
        type: Object,
    },

    deliveryTickets: {
        required: true,
        type: Array,
    },
    totalPurchasingPrice: {
        required: true,
        type: String,
        default: '',
    },
    totalSellingPrice: {
        required: true,
        type: String,
        default: '',
    },
    user: {
        required: true,
        type: Object,
    },
    permissions: {
        required: true,
        type: Object,
    },
})

const deliveryTicketFormRef = ref(null)
const deliveryTicketImportFormRef = ref(null)
</script>
<template>
    <div class="fill-height tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                id="dropdown-example"
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="'Go to order details'"
                    icon
                    class="hidden-xs-only"
                    @click="$inertia.get($route('orders.show', { order }))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('order.delivery_tickets.order-delivery-tickets') }}
                </v-toolbar-title>

                <v-spacer />
                <div class="tw-mr-2">
                    <span class="tw-block tw-text-xs">{{
                        $t('delivery_tickets.labels.total-purchasing-price')
                    }}</span>
                    <span class="tw-text-lg tw-font-bold">{{
                        totalPurchasingPrice
                    }}</span>
                </div>
                <div class="tw-mr-2">
                    <span class="tw-block tw-text-xs">{{
                        $t('delivery_tickets.labels.total-selling-price')
                    }}</span>
                    <span class="tw-text-lg tw-font-bold">{{
                        totalSellingPrice
                    }}</span>
                </div>
                <v-btn
                    color="info"
                    @click.prevent="$refs.deliveryTicketImportFormRef.show()"
                >
                    {{ $t('buttons.import') }}
                </v-btn>
                <v-btn
                    color="info"
                    @click.prevent="$refs.deliveryTicketFormRef.show()"
                >
                    {{ $t('buttons.create') }}
                </v-btn>
            </v-toolbar>
        </portal>

        <v-container
            v-if="
                !deliveryTickets.length &&
                authCan('can_manage_order_delivery_tickets')
            "
            grid-list-xs
            fill-height
        >
            <v-layout
                align-center
                justify-center
                fill-height
            >
                <v-flex xs12>
                    <JumboUploadButton
                        :title="$t('order.delivery_tickets.add-delivery')"
                        icon="add_shopping_cart"
                        @click:card="() => $refs.deliveryTicketFormRef.show()"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-container
            v-else
            grid-list-lg
            text-xs-center
            px-0
            class="!tw-max-w-full"
        >
            <v-layout
                row
                wrap
            >
                <v-flex
                    v-for="deliveryTicket in deliveryTickets"
                    :key="deliveryTicket.id"
                    xs12
                    sm4
                >
                    <v-card
                        tile
                        hover
                        @click="
                            $inertia.get(
                                $route('orders.show.delivery_tickets.show', {
                                    order,
                                    deliveryTicket,
                                }),
                            )
                        "
                    >
                        <div class="tw-px-4 tw-pt-4 tw-mb-1.5 tw-relative">
                            <div
                                v-if="Array.isArray(deliveryTicket.deliverer)"
                                class="tw-absolute tw-right-0 tw-px-4"
                            >
                                <v-icon color="red">info</v-icon>
                            </div>
                            <div
                                class="tw-flex tw-items-start tw-justify-between tw-w-full sm:tw-flex-col"
                            >
                                <div
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-2">
                                        Total Purchasing Price:
                                    </span>
                                    <span class="tw-text-base tw-font-bold">{{
                                        deliveryTicket.total_purchasing_price_formatted
                                    }}</span>
                                </div>
                            </div>
                            <div class="tw-w-full sm:tw-flex-col">
                                <div
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-1">
                                        Total Selling Price :
                                    </span>

                                    <span class="tw-text-base tw-font-bold">
                                        {{
                                            deliveryTicket.total_selling_price_formatted
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="tw-px-4 tw-pb-4">
                            <div
                                class="tw-flex tw-items-start tw-justify-between tw-w-full tw-mb-1.5 sm:tw-flex-col"
                            >
                                <div
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-2">
                                        Internal ID:
                                    </span>
                                    <span class="tw-text-base tw-font-bold">{{
                                        deliveryTicket.internal_id
                                    }}</span>
                                </div>
                                <div
                                    v-if="deliveryTicket.external_id"
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-1">
                                        External ID:
                                    </span>
                                    <span class="tw-text-base tw-font-bold">{{
                                        deliveryTicket.external_id
                                    }}</span>
                                </div>
                            </div>
                            <div
                                class="tw-flex tw-items-start tw-justify-between tw-w-full sm:tw-flex-col"
                            >
                                <div
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-1">
                                        Delivery Name:
                                    </span>

                                    <span class="tw-text-base tw-font-bold">
                                        {{
                                            !Array.isArray(
                                                deliveryTicket.deliverer,
                                            )
                                                ? deliveryTicket.deliverer
                                                      .column_values?.name
                                                : 'N/A'
                                        }}
                                    </span>
                                </div>
                                <div
                                    v-if="deliveryTicket.delivery_date"
                                    class="title font-weight-light tw-items-center tw-cursor-pointer hover:tw-underline tw-flex tw-w-full"
                                >
                                    <span class="tw-text-sm tw-mr-1">
                                        Delivery Date:
                                    </span>
                                    <span class="tw-text-base tw-font-bold">{{
                                        deliveryTicket.delivery_date
                                    }}</span>
                                </div>
                            </div>
                        </div>
                        <v-card-text class="pt-0">
                            <v-icon
                                size="70"
                                :color="
                                    deliveryTicket.is_paid
                                        ? 'success'
                                        : 'default'
                                "
                            >
                                local_shipping
                            </v-icon>
                        </v-card-text>
                        <v-divider />
                        <v-card-actions class="tw-justify-between">
                            <div class="tw-flex tw-gap-x-2 tw-items-center">
                                <div>{{ $t('labels.invoice-status') }}:</div>
                                <v-chip
                                    :color="
                                        deliveryTicket.is_paid
                                            ? 'success'
                                            : 'default'
                                    "
                                    dark
                                    label
                                    small
                                >
                                    {{ deliveryTicket.is_paid_label }}
                                </v-chip>
                            </div>
                            <v-icon class="">
                                {{
                                    deliveryTicketIsIncoming(
                                        deliveryTicket.type.value,
                                    )
                                        ? 'file_download'
                                        : 'file_upload'
                                }}
                            </v-icon>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
        <DeliveryTicketForm
            ref="deliveryTicketFormRef"
            :order="order"
            @refresh="
                () =>
                    $inertia.reload({
                        only: ['deliveryTickets', 'order'],
                    })
            "
        />
        <DeliveryTicketImportForm
            ref="deliveryTicketImportFormRef"
            :order="order"
            @refresh="
                () =>
                    $inertia.reload({
                        only: ['deliveryTickets', 'order'],
                    })
            "
        />
    </div>
</template>
