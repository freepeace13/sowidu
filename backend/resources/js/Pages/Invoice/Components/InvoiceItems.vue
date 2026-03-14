<script setup>
import { authCan } from '@/Composables/useAuth'
import { useGetPageProps } from '@/Composables/useGetPageProps'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import { router, useForm } from '@inertiajs/vue2'
import { computed, onMounted, ref } from 'vue'
import EditInvoiceItemForm from './EditInvoiceItemForm.vue'
import EditInvoiceItemRateForm from './EditInvoiceItemRateForm.vue'
import InvoiceItem from './InvoiceItem.vue'
import InvoiceItemDetails from './InvoiceItemDetails.vue'
import InvoiceSummary from './InvoiceSummary.vue'

const props = defineProps({
    invoice: {
        type: [Object],
    },
    items: {
        required: false,
        type: Array,
        default: () => [],
    },
    showActions: {
        required: false,
        type: Boolean,
        default: true,
    },
    showUpdateQuantity: {
        required: false,
        type: Boolean,
        default: true,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()

const editInvoiceItemRateForm = ref(null)
const editInvoiceItemRef = ref(null)

const form = useForm({
    quantity: 0,
})

const headers = computed(() => {
    const constantHeaders = [
        {
            text: $t('invoices.items.labels.name'),
            value: 'name',
            sortable: false,
            align: 'left',
            width: '40%',
        },
        {
            text: $t('invoices.labels.quantity'),
            value: 'quantity',
            sortable: false,
            align: 'center',
        },
        {
            text: $t('invoices.preview.table.item-unit'),
            value: 'unit_name',
            sortable: false,
            align: 'center',
        },
        {
            text: $t('invoices.preview.table.price-per-piece'),
            value: 'quantity',
            sortable: false,
            align: 'center',
        },
        {
            text: $t('invoices.preview.table.total-price'),
            sortable: false,
            align: 'center',
        },
    ]
    let actions = []

    if (props.showActions) {
        actions = [
            {
                text: $t('labels.actions'),
                value: 'action',
                sortable: false,
                align: 'center',
            },
        ]
    }
    return [...constantHeaders, ...actions]
})

const colSpan = computed(
    () => headers.value.length - (props.showActions ? 2 : 1),
)

const isFetchingInvoiceSummary = ref(true)
const invoiceSummary = computed(() =>
    getPageProps('invoiceSummary', {
        deductions: [],
        taxes: [],
    }),
)

onMounted(() => {
    isFetchingInvoiceSummary.value = true
    setTimeout(() => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['invoiceSummary'],
            onSuccess: () => {
                isFetchingInvoiceSummary.value = false

                let reloadProps = ['documents']

                if (authCan('can_view_payments')) {
                    reloadProps = [
                        ...reloadProps,
                        'payments',
                        'paymentsSummary',
                    ]
                }

                router.reload({
                    preserveState: true,
                    preserveScroll: true,
                    only: reloadProps,
                })
            },
        })
    }, 2000)
})

function confirmRemoving(item) {
    const invoice = useGetPageProps('invoice')
    $confirm({
        title: $t('labels.delete'),
        question: $t('invoices.message.items.confirm-deleting'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('invoices.items.destroy', {
                    item,
                    invoice,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['items', 'totalPrice', 'invoice', 'invoiceSummary'],
                },
            )
        },
    })
}

function updateQuantity(item, quantity) {
    const invoice = getPageProps('invoice')
    form.transform((data) => ({ ...data, quantity })).patch(
        $route('invoices.items.update', {
            invoice,
            item,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['items', 'totalPrice', 'invoice'],
        },
    )
}
</script>
<template>
    <v-card flat>
        <EditInvoiceItemForm ref="editInvoiceItemRef" />
        <EditInvoiceItemRateForm ref="editInvoiceItemRateForm" />
        <v-card-text class="pa-0">
            <v-alert
                :value="!items.length"
                color="info"
                icon="info"
                outline
                class="pa-2"
            >
                <div class="!tw-flex !tw-items-center tw-justify-between">
                    <div>
                        {{ $t('invoices.message.items.empty') }}
                    </div>
                    <v-btn
                        color="info"
                        small
                        @click="$emit('click:add-item', invoice)"
                    >
                        {{ $t('invoices.labels.add-item') }}
                    </v-btn>
                </div>
            </v-alert>
            <v-data-table
                v-show="items.length"
                :headers="headers"
                :items="items"
                :hide-actions="true"
                :no-data-text="$t('invoices.message.items.empty')"
                :expand="false"
                item-key="id"
                class="elevation-2 px-0 py-2 dense-header columns-center table-invoice-items"
            >
                <template #items="itemProps">
                    <InvoiceItem
                        :key="itemProps.item.id"
                        :item="itemProps.item"
                        :is-updating-quantity="form.processing"
                        :show-actions="showActions"
                        :show-update-quantity="showUpdateQuantity"
                        :is-expanded="itemProps.expanded"
                        @click:update-rate="
                            (item) => $refs.editInvoiceItemRateForm.show(item)
                        "
                        @click:update-quantity="
                            ({ item, quantity }) =>
                                updateQuantity(item, quantity)
                        "
                        @click:remove="(item) => confirmRemoving(item)"
                        @click:show-more="
                            () => (itemProps.expanded = !itemProps.expanded)
                        "
                        @click:edit-quantity-price="
                            (item) => editInvoiceItemRef.show(item)
                        "
                    />
                </template>
                <template #footer>
                    <tr
                        v-for="loader in 3"
                        v-show="isFetchingInvoiceSummary"
                        :key="`loader-${loader}`"
                    >
                        <td
                            :colspan="colSpan"
                            class="tw-text-right"
                        >
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="20"
                            />
                        </td>
                        <td class="tw-whitespace-nowrap tw-text-center">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                                size="20"
                            />
                        </td>
                    </tr>
                    <InvoiceSummary
                        v-if="!isFetchingInvoiceSummary"
                        :col-span="colSpan"
                        :invoice-summary="invoiceSummary"
                    />
                </template>
                <template #expand="{ item: { details }, item }">
                    <!-- TODO: Request on getting invoice item details via API to reduce this page load -->
                    <v-card flat>
                        <InvoiceItemDetails
                            :details="details"
                            :item="item"
                        />
                    </v-card>
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>
<style scoped>
.dense-header {
    :deep(table thead tr) {
        height: 40px;

        th {
            height: 36px;
        }
    }
}

/* .table-invoice-items {
    :deep(table) {
        tbody {
            display: block;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
        }

        thead,
        tfoot {
            flex-shrink: 0;
        }

        tfoot {
            @apply tw-bg-gray-100;
        }
    }

    :deep(table thead tr),
    :deep(table tfoot tr),
    :deep(table tbody tr) {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    :deep(table) th:nth-child(1),
    :deep(table) td:nth-child(1) {
        width: 40%;
    }
} */
</style>
