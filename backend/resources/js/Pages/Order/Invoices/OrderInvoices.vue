<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import JumboUploadButton from '../Files/Components/JumboUploadButton.vue'
import InvoiceForm from '@/Pages/Invoice/Components/InvoiceForm.vue'
import { authCan } from '@/Composables/useAuth'
import { computed, ref } from 'vue'
import MenuListItem from '@/Components/MenuListItem.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
const props = defineProps({
    order: {
        required: true,
        type: Object,
    },

    invoices: {
        required: true,
        type: Array,
    },
    permissions: {
        required: true,
        type: Object,
    },
    invoiceTypes: {
        required: true,
        type: Array,
    },
    authCompany: {
        type: Object,
        required: true,
    },
})

const invoiceFormRef = ref(null)
const invoiceDefaults = computed(() => props.authCompany?.invoice_defaults)
</script>
<template>
    <div class="fill-height tw-w-full">
        <InvoiceForm
            ref="invoiceFormRef"
            :order="order"
            :invoice-defaults="invoiceDefaults"
            @refresh="() => $inertia.reload({ only: ['invoices'] })"
        />

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
                    {{ $t('order.labels.order-invoices') }}
                </v-toolbar-title>

                <v-spacer />
                <v-menu
                    v-if="authCan('can_manage_order_invoices')"
                    offset-y
                    left
                >
                    <template #activator="{ on }">
                        <v-btn
                            color="info"
                            v-on="on"
                        >
                            {{ $t('buttons.create') }}
                        </v-btn>
                    </template>
                    <v-list dense>
                        <MenuListItem
                            icon="move_to_inbox"
                            @click="invoiceFormRef.show(null, 'incoming')"
                        >
                            {{ $t('order.labels.add-incoming-invoice') }}
                        </MenuListItem>
                        <MenuListItem
                            icon="outbox"
                            @click="invoiceFormRef.show(null, 'outgoing')"
                        >
                            {{ $t('order.labels.add-outgoing-invoice') }}
                        </MenuListItem>
                    </v-list>
                </v-menu>
            </v-toolbar>
        </portal>

        <v-container
            v-if="!invoices.length && authCan('can_manage_order_invoices')"
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
                        :title="$t('order.invoices.add-invoice')"
                        icon="post_add"
                        @click:card="() => invoiceFormRef.show()"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-container
            grid-list-lg
            text-xs-center
            px-0
            class="!tw-max-w-full"
        >
            <!-- <v-layout row wrap>
                <v-flex>
                </v-flex>
            </v-layout> -->
            <v-layout
                row
                wrap
            >
                <v-flex xs12>
                    <v-alert
                        :value="!invoices.length"
                        color="info"
                        icon="info"
                        outline
                    >
                        {{ $t('order.invoices.messages.no-invoices') }}
                    </v-alert>
                </v-flex>
                <v-flex
                    v-for="invoice in invoices"
                    :key="invoice.id"
                    xs12
                    sm4
                >
                    <v-card tile>
                        <v-card-title
                            class="tw-flex tw-flex-col tw-text-left !tw-items-start"
                        >
                            <div
                                class="title font-weight-light tw-cursor-pointer hover:tw-underline"
                                @click="
                                    $inertia.get(
                                        $route('orders.show.invoices.show', {
                                            order,
                                            invoice,
                                        }),
                                    )
                                "
                            >
                                {{ invoice.external_id ?? invoice.internal_id }}
                            </div>
                            <v-chip
                                :color="invoice.status.color"
                                dark
                                label
                                small
                                class=""
                            >
                                <v-icon
                                    small
                                    left
                                >
                                    {{ invoice.status.icon }}
                                </v-icon>
                                {{ invoice.status.label }}
                            </v-chip>
                        </v-card-title>
                        <v-card-text>
                            <v-icon
                                size="60"
                                :color="invoice.status.color"
                            >
                                receipt_long
                            </v-icon>
                        </v-card-text>
                        <v-divider />
                        <v-card-actions class="tw-justify-between">
                            <div class="tw-flex tw-gap-x-2">
                                <div class="tw-font-semibold tw-capitalize">
                                    {{ $t('invoices.labels.total') }}:
                                </div>
                                <div>{{ invoice.total_amount_formatted }}</div>
                            </div>
                            <v-btn
                                :color="invoice.status.color"
                                flat
                                @click="
                                    $inertia.get(
                                        $route('orders.show.invoices.show', {
                                            order,
                                            invoice,
                                        }),
                                    )
                                "
                            >
                                {{ $t('buttons.open') }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>
