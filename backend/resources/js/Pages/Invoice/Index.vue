<script setup>
import { useGetUserTimezone } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import { useInfiniteScroll, useWindowSize } from '@vueuse/core'
import { ref, watch } from 'vue'
import InvoiceForm from './Components/InvoiceForm.vue'
import InvoicePaymentForm from './Components/InvoicePaymentForm.vue'
import InvoiceRow from './Components/InvoiceRow.vue'
import InvoiceToolbar from './Components/InvoiceToolbar.vue'
import InvoicesSummaries from './Components/InvoicesSummaries.vue'

const props = defineProps({
    permissions: {
        type: Object,
        required: true,
    },
    invoiceDefaults: {
        type: Object,
        required: true,
    },
    invoices: {
        required: false,
        type: Array,
        default: () => [],
    },
    paginator: {
        required: false,
        type: Object,
    },
    summaryTotal: {
        required: false,
        type: Object,
        default: () => ({
            withoutVat: '0',
            vat: '0',
            withVat: '0',
        }),
    },
    filters: {
        required: true,
        type: Object,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()

const items = ref([])
const selected = ref([])
const isBulkExporting = ref(false)
const isLoading = ref(false)
const pageFilters = ref(props.filters)
const invoiceSummariesRef = ref(null)
const invoicePaymentFormRef = ref(null)
const headers = [
    { text: 'id', sortable: false },
    { text: $t('invoices.labels.invoice-type'), sortable: false },
    { text: $t('invoices.labels.invoice-no'), sortable: false },
    { text: $t('order.labels.client'), sortable: false },
    { text: $t('invoices.labels.amount'), sortable: false, align: 'right' },
    { text: $t('order.labels.order-number'), sortable: false },
    { text: $t('labels.status'), sortable: false },
    { text: $t('order.labels.delivery-address'), sortable: false },
    { text: $t('order.labels.delivery-date'), sortable: false },
    { text: $t('labels.actions'), sortable: false, align: 'right' },
]

watch(
    pageFilters,
    (newFilters) => {
        fetch(1, newFilters)
    },
    {
        deep: true,
    },
)

function fetch(page = 1, query = {}) {
    if (page === 1) {
        items.value = []
        invoiceSummariesRef.value?.fetch(query)
    }

    router.reload({
        only: ['invoices', 'paginator', 'filters'],
        data: {
            page,
            ...query,
            timezone: useGetUserTimezone(),
        },
        onSuccess: ({ props: { invoices, filters } }) => {
            items.value = [...items.value, ...invoices]

            responsivePaginator(filters)
        },
        onStart: () => {
            isLoading.value = true
        },
        onFinish: () => {
            isLoading.value = false
        },
    })
}

function attachInfiniteScroll() {
    useInfiniteScroll(
        window,
        () => {
            const { has_more_pages, next_page } = props.paginator

            if (!has_more_pages) return
            fetch(next_page, pageFilters.value)
        },
        { distance: 10 },
    )
}

attachInfiniteScroll()
fetch()

function responsivePaginator(filters) {
    const { current_page, has_more_pages, next_page } = props.paginator
    const listHeight = 1024 * current_page
    const { height } = useWindowSize()
    if (height.value > listHeight) {
        if (has_more_pages) {
            fetch(next_page, filters) // Fetch next page
        }
    }
}

function confirmDeleting(invoice) {
    $confirm({
        title: $t('labels.delete'),
        question: $t('invoices.message.confirm_deleting'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('invoices.destroy', {
                    invoice,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'flash'],
                    onSuccess: () => {
                        fetch(1, pageFilters.value)
                    },
                },
            )
        },
    })
}

function bulkExport() {
    router.post(
        $route('invoices.bulk_export'),
        {
            invoice_ids: selected.value.map((item) => item.id),
            filters: pageFilters.value,
        },
        {
            onStart: () => {
                isBulkExporting.value = true
            },
            onFinish: () => {
                isBulkExporting.value = false
            },
        },
    )
}
</script>
<template>
    <div class="tw-h-full">
        <v-container
            fluid
            pt-2
            grid-list-md
        >
            <v-layout
                row
                wrap
            >
                <InvoiceToolbar
                    :page-filters="pageFilters"
                    :is-loading="isLoading || isBulkExporting"
                    :enable-exporting="!!selected.length"
                    @update:filters="(filterValues) => fetch(1, filterValues)"
                    @click:create-invoice="
                        (type) => $refs.invoiceFormRef.show(null, type)
                    "
                    @click:bulk-export="bulkExport"
                />
            </v-layout>
            <!-- Invoice Summaries -->
            <InvoicesSummaries ref="invoiceSummariesRef" />

            <v-layout
                justify-start
                column
                fill-height
            >
                <div class="mt-2">
                    <v-subheader
                        class="!tw-px-0 md:tw-items-center tw-justify-end"
                    >
                        Showing
                        {{ items.length }} of {{ paginator?.total }} results.
                    </v-subheader>
                </div>

                <div class="tw-flex tw-w-full tw-max-h-full">
                    <v-flex
                        xs12
                        class="!tw-overflow-auto elevation-10"
                    >
                        <v-data-table
                            id="itemsContainer"
                            v-model="selected"
                            :headers="headers"
                            :items="items"
                            :loading="isLoading"
                            hide-actions
                            item-key="id"
                            select-all
                        >
                            <template #items="itemProps">
                                <InvoiceRow
                                    :selected="itemProps.selected"
                                    :invoice="itemProps.item"
                                    :can-be-updated="
                                        (permissions['can_manage_invoices'] ??
                                            false) &&
                                        itemProps.item.can_be_edited
                                    "
                                    :can-be-deleted="
                                        (permissions['can_manage_invoices'] ??
                                            false) &&
                                        itemProps.item.can_be_edited
                                    "
                                    @click:show-materials="
                                        itemProps.expanded = !itemProps.expanded
                                    "
                                    @click:edit="
                                        (invoice) =>
                                            $refs.invoiceFormRef.show(invoice)
                                    "
                                    @click:delete="
                                        (invoice) => confirmDeleting(invoice)
                                    "
                                    @click:show-details="
                                        (invoice) =>
                                            router.get(
                                                $route('invoices.show', {
                                                    invoice,
                                                }),
                                            )
                                    "
                                    @click:add-payment="
                                        (invoice) =>
                                            $refs.invoicePaymentFormRef.show(
                                                null,
                                                invoice,
                                            )
                                    "
                                >
                                    <template #checkbox>
                                        <td>
                                            <VCheckbox
                                                v-model="itemProps.selected"
                                                :input-value="itemProps.item.id"
                                                primary
                                                hide-details
                                            />
                                        </td>
                                    </template>
                                </InvoiceRow>
                            </template>
                            <template #footer>
                                <tr
                                    v-for="n in 4"
                                    v-show="isLoading"
                                    :key="n"
                                >
                                    <td
                                        v-for="i in headers.length"
                                        :key="i"
                                    >
                                        <div
                                            class="tw-w-full tw-divide-y tw-rounded tw-animate-pulse tw-mt-3"
                                        >
                                            <div
                                                class="tw-h-4 tw-bg-gray-300 tw-rounded-full tw-w-full tw-mb-2.5"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-flex>
                </div>
            </v-layout>
        </v-container>

        <InvoiceForm
            ref="invoiceFormRef"
            :invoice-defaults="invoiceDefaults"
            @refresh="fetch"
        />

        <InvoicePaymentForm ref="invoicePaymentFormRef" />
    </div>
</template>
<style lang="scss" scoped>
#itemsContainer {
    .v-table__overflow {
        .v-datatable {
            position: relative;

            thead {
                tr {
                    th {
                        position: sticky;
                        top: 0;
                        background: #fff;
                    }
                }
            }
        }
    }
}
</style>
