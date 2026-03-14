<script setup>
import { useGetUserTimezone } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import { useDebounceFn, useInfiniteScroll, useWindowSize } from '@vueuse/core'
import axios from 'axios'
import qs from 'qs'
import { reactive, ref } from 'vue'
import DeliveryTicketForm from '../Components/DeliveryTicketForm.vue'
import DeliveryTicketRow from '../Components/DeliveryTicketRow.vue'
import DeliveryTicketsToolbar from '../Components/DeliveryTicketsToolbar.vue'

defineProps({
    allowedTypes: {
        type: Array,
        required: true,
    },
    deliveryTicketTypes: {
        type: Array,
        required: true,
    },
    companyCurrency: {
        type: Object,
        required: true,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()

const items = ref([])
const deliveryTicketFormRef = ref(null)

const pagination = reactive({
    current_page: 0,
    next_page_url: null,
    isLoading: true,
    total: 0,
    partialCount: 0,
})

const headers = [
    { text: $t('delivery_tickets.form.ticket-no'), sortable: false },
    { text: $t('delivery_tickets.form.external_id'), sortable: false },
    { text: $t('delivery_tickets.form.deliverer'), sortable: false },
    { text: $t('order.labels.order-number'), sortable: false },
    {
        text: $t('delivery_tickets.form.delivery-address'),
        sortable: false,
    },
    { text: $t('delivery_tickets.form.delivery_date'), sortable: false },
    {
        text: $t('delivery_tickets.labels.total-purchasing-price'),
        sortable: false,
        align: 'center',
    },
    {
        text: $t('delivery_tickets.labels.total-selling-price'),
        sortable: false,
        align: 'center',
    },
    { text: $t('labels.actions'), sortable: false, align: 'right' },
    { text: $t('headings.invoices'), sortable: false, align: 'center' },
]

const fetch = useDebounceFn(async (page = 1, filters = {}) => {
    try {
        pagination.isLoading = true

        if (page == 1) {
            items.value = []
            pagination.current_page = 0
            pagination.next_page_url = null
            pagination.partialCount = 0
            pagination.total = 0
        }

        const {
            data: { data, current_page, next_page_url, total, per_page },
        } = await axios.get($route('json.delivery_tickets.index'), {
            params: {
                page,
                ...filters,
                timezone: useGetUserTimezone(),
            },
            paramsSerializer: (params) => {
                // Remove null values from params
                return qs.stringify(
                    Object.fromEntries(
                        Object.entries(params).filter(
                            ([, value]) => value !== null,
                        ),
                    ),
                    { arrayFormat: 'brackets' },
                )
            },
        })

        items.value = [...items.value, ...data]
        pagination.current_page = current_page
        pagination.next_page_url = next_page_url
        pagination.total = total
        pagination.partialCount = calculatePartialCount(
            per_page,
            current_page,
            total,
        )

        responsivePaginator()
    } catch (errors) {
        console.error(errors)
    } finally {
        pagination.isLoading = false
    }
}, 500)

function attachInfiniteScroll() {
    useInfiniteScroll(
        window,
        () => {
            if (pagination.isLoading || !pagination.next_page_url) return
            fetch(pagination.current_page + 1)
        },
        { distance: 10 },
    )
}

attachInfiniteScroll()
fetch()

function responsivePaginator() {
    const listHeight = 740 * pagination.current_page
    const { height } = useWindowSize()

    if (height.value > listHeight) {
        // Fetch next page
        if (pagination.next_page_url) {
            fetch(pagination.current_page + 1)
        }
    }
}

function calculatePartialCount(per_page, current_page, total) {
    return per_page * current_page > total ? total : per_page * current_page
}

function confirmDeleting(deliveryTicket) {
    $confirm({
        title: $t('labels.delete'),
        question: $t('delivery_tickets.message.confirm_deleting'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('delivery_tickets.destroy', {
                    deliveryTicket,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'flash'],
                    onSuccess: () => {
                        fetch()
                    },
                },
            )
        },
    })
}

// function showMaterialForm(deliveryTicket) {
//     deliveryTicketOpened.value = deliveryTicket

//     materialForm.value.show(
//         $route('delivery_tickets.materials.store', {
//             deliveryTicket,
//         }),
//     )
// }
</script>
<template>
    <div class="tw-h-full mt-2">
        <v-container
            fluid
            pt-0
            grid-list-md
        >
            <v-layout
                row
                wrap
                fill-height
            >
                <DeliveryTicketsToolbar
                    :is-loading="pagination.isLoading"
                    @filter:changed="(filters) => fetch(1, filters)"
                    @click:create="() => deliveryTicketFormRef.show()"
                />
            </v-layout>

            <v-layout
                row
                wrap
                fill-height
            >
                <div class="mt-2 tw-w-full">
                    <v-subheader
                        class="!tw-px-0 md:tw-items-center tw-justify-end"
                    >
                        Showing {{ pagination.partialCount }} of
                        {{ pagination.total }} results.
                    </v-subheader>
                </div>
                <div class="tw-flex tw-w-full tw-h-full tw-max-h-full">
                    <v-flex
                        xs12
                        class="!tw-overflow-auto elevation-10"
                    >
                        <v-data-table
                            id="listContainer"
                            :headers="headers"
                            :items="items"
                            :loading="pagination.isLoading"
                            hide-actions
                            item-key="id"
                            :expand="false"
                        >
                            <template #items="props">
                                <DeliveryTicketRow
                                    :delivery-ticket="props.item"
                                    :editable="!props.item.invoices.length"
                                    :deletable="!props.item.invoices.length"
                                    @click:show-materials="
                                        (deliveryTicket) =>
                                            router.get(
                                                $route(
                                                    'delivery_tickets.show',
                                                    { deliveryTicket },
                                                ),
                                            )
                                    "
                                    @click:edit="
                                        (deliveryTicket) =>
                                            $refs.deliveryTicketFormRef.show(
                                                deliveryTicket,
                                            )
                                    "
                                    @click:delete="
                                        (deliveryTicket) =>
                                            confirmDeleting(deliveryTicket)
                                    "
                                    @click:show-details="
                                        (deliveryTicket) =>
                                            router.get(
                                                $route(
                                                    'delivery_tickets.show',
                                                    { deliveryTicket },
                                                ),
                                            )
                                    "
                                >
                                    <template #additional>
                                        <td>
                                            <div
                                                v-if="
                                                    !props.item.invoices.length
                                                "
                                                class="caption font-weight-thin tw-text-center tw-italic tw-text-gray-400"
                                            >
                                                {{
                                                    $t(
                                                        'delivery_tickets.invoices.no-invoice',
                                                    )
                                                }}
                                            </div>
                                            <ul class="tw-list-disc">
                                                <li
                                                    v-for="invoice in props.item
                                                        .invoices"
                                                    :key="invoice.id"
                                                >
                                                    <div
                                                        class="tw-flex tw-gap-x-2 tw-items-center"
                                                    >
                                                        <a
                                                            :href="
                                                                $route(
                                                                    'invoices.show',
                                                                    {
                                                                        invoice,
                                                                    },
                                                                )
                                                            "
                                                            target="_blank"
                                                            class="blue--text info--text hover:tw-underline tw-font-semibold tw-cursor-pointer tw-text-xs"
                                                        >
                                                            {{
                                                                invoice.internal_id
                                                            }}
                                                        </a>
                                                        <v-btn
                                                            flat
                                                            icon
                                                            :color="
                                                                invoice.status
                                                                    .color
                                                            "
                                                            small
                                                            class="ma-0"
                                                        >
                                                            <v-icon small>
                                                                {{
                                                                    invoice
                                                                        .status
                                                                        .icon
                                                                }}
                                                            </v-icon>
                                                        </v-btn>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                    </template>
                                </DeliveryTicketRow>
                            </template>
                            <!-- <template #expand="{ item }">
                                <DeliveryTicketMaterialsRow
                                    :delivery-ticket="item"
                                    :no-actions="item.is_paid"
                                    @click:add-materials="
                                        (deliveryTicket) =>
                                            showMaterialForm(deliveryTicket)
                                    "
                                />
                            </template> -->
                            <template #footer>
                                <tr
                                    v-for="n in 4"
                                    v-show="pagination.isLoading"
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

        <DeliveryTicketForm
            ref="deliveryTicketFormRef"
            @refresh="fetch"
        />

        <!-- <OrderProductForm
            ref="materialForm"
            :title="$t('delivery_tickets.labels.add-materials-to-delivery')"
            :submit-btn-text="
                $t('delivery_tickets.buttons.add-to-delivery-ticket')
            "
            @refresh="refreshDeliveryTicketMaterials"
        /> -->

        <!-- <DeliveryTicketDetailsModal
            ref="detailsModal"
            @click:add-materials="
                (deliveryTicket) => showMaterialForm(deliveryTicket)
            "
        /> -->
    </div>
</template>
<style lang="scss">
.vue-daterange-picker {
    .form-control {
        border: none;
        @apply tw-p-0;
    }
}

#listContainer {
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
