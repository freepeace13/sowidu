<template>
    <div class="tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <OrderToolbar
                :is-loading="isLoading"
                :initial-search="filters.q"
                :order-statuses="orderStatuses"
                @filter="(e) => filterOrders(e)"
            />
        </portal>

        <v-layout
            row
            wrap
            align-center
        >
            <v-flex>
                <v-btn
                    color="primary"
                    :disabled="user | deny('can create order')"
                    :small="$vuetify.breakpoint.xsOnly"
                    @click="$refs.incomingOrderForm.show()"
                >
                    <v-icon class="mr-1">add</v-icon>
                    {{
                        $vuetify.breakpoint.xs
                            ? ''
                            : $tc('order.incoming.buttons.create')
                    }}
                </v-btn>
            </v-flex>
            <v-spacer />
            <v-flex
                tw-text-right
                tw-font-bold
                tw-text-base
            >
                {{ total }}
                {{
                    `${$t('order.labels.incoming')} ${$tc(
                        'order.labels.order',
                        total,
                    )}`
                }}
            </v-flex>
        </v-layout>
        <v-layout
            align-start
            column
        >
            <v-flex
                xs12
                grow
                w-full
                mt-2
            >
                <v-data-table
                    id="listContainer"
                    ref="fileListingContainer"
                    :headers="headers"
                    :headers-length="headers.length"
                    :items="orders"
                    :loading="isLoading"
                    :must-sort="false"
                    :pagination.sync="pagination"
                    :total-items="orders.length"
                    class="elevation-1"
                    hide-actions
                    no-data-text="No incoming orders available."
                >
                    <template #items="{ item: { client, ...order }, item }">
                        <OrderRow
                            :order="order"
                            :from="client"
                            @click:more="
                                (e) => $refs.itemActionMenu.show(e, item)
                            "
                        />
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>

        <IncomingOrderForm
            ref="incomingOrderForm"
            @refresh="refresh"
        />

        <ItemActionMenu
            ref="itemActionMenu"
            @click:update="(order) => $refs.editOrderForm.show(order)"
            @click:delete="(order) => destroy(order)"
            @click:details="
                (order) => {
                    $inertia.get(
                        $route('orders.show', {
                            order,
                        }),
                    )
                }
            "
        />

        <EditOrderForm
            ref="editOrderForm"
            route="orders.incoming.update"
            :refresh-props="[]"
            @refresh="refresh"
        />
    </div>
</template>
<script>
// TODO delete this file if not used - currently using `Order/IncomingOrders.vue`
import AuthLayout from '@/Layouts/AuthLayout.vue'
import ItemActionMenu from '@components/Menus/ItemActionMenu.vue'
import { useDebounceFn } from '@vueuse/shared'
import EditOrderForm from '../Components/EditOrderForm.vue'
import OrderToolbar from '../Components/OrderToolbar.vue'
import OrderLayout from '../OrderLayout.vue'
import IncomingOrderForm from './Components/IncomingOrderForm.vue'
import OrderRow from '@/Features/Order/Components/OrderRow.vue'
import { grant, deny } from '@/Composables/usePermissionFilters'
import { useInfiniteScroll } from '@vueuse/core'

export default {
    components: {
        OrderToolbar,
        IncomingOrderForm,
        ItemActionMenu,
        EditOrderForm,
        OrderRow,
    },
    filters: {
        grant: (user, permission) => grant(user, permission),
        deny: (user, permission) => deny(user, permission),
    },
    layout: [AuthLayout, OrderLayout],

    props: {
        incomingOrders: {
            required: true,
            type: Object,
        },
        filters: {
            required: true,
            type: [Object, Array],
            default: () => ({}),
        },
        user: {
            required: true,
            type: Object,
        },
        orderStatuses: {
            required: false,
            type: Array,
            default: () => [],
        },
    },

    data: (vm) => ({
        headers: [
            { text: 'Name', sortable: false },
            { text: 'Description', sortable: false },
            { text: 'Planned Start - Finished Date', sortable: false },
            {
                text: 'Status',
                align: 'center',
                value: 'status',
                sortable: true,
            },
            { text: 'Actions', sortable: false, align: 'right' },
        ],
        isLoading: false,
        pagination: {
            page: 1,
            rowsPerPage: 20,
            sortBy: null,
            descending: false,
        },
        orders: vm.incomingOrders.data,
        initialUrl: vm.$page.url,
    }),

    computed: {
        total() {
            return this.orders.length
        },

        initialFilter() {
            return this.filters?.initial
        },
    },

    watch: {
        pagination: {
            handler(newValue, oldValue) {
                if (
                    newValue.sortBy != oldValue.sortBy ||
                    newValue.descending != oldValue.descending
                ) {
                    const { page, sortBy, descending } = newValue
                    this.orders = []
                    this.fetch(page, {
                        sortBy,
                        descending,
                    })
                }
            },
            deep: true,
        },
    },

    created() {
        this.filterOrders = useDebounceFn(async (filters) => {
            this.orders = []
            this.fetch(1, filters)
        })
    },

    mounted() {
        const company = this.user?.tenant?.id

        if (!company) return

        window.Echo.private(`company.${company}.orders`).listenToAll(() => {
            this.$inertia.reload({
                only: ['unreadNotifications', 'incomingOrders'],
            })
        })

        useInfiniteScroll(
            this.$refs.fileListingContainer,
            () => {
                if (!this.incomingOrders.next_page_url) return

                this.fetch(this.incomingOrders.current_page + 1)
            },
            { distance: 30 },
        )
    },

    beforeDestroy() {
        const company = this.user?.tenant?.id

        if (!company) return

        window.Echo.leave(`company.${company}.orders`)
    },

    methods: {
        destroy(order) {
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this order?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('orders.incoming.destroy', { order }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['incomingOrders'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Order has been deleted.',
                                )
                            },
                        },
                    )
                },
            })
        },

        refresh() {
            this.orders = []
            this.fetch(1, this.filters)
        },

        fetch(page = 1, filters = {}) {
            this.$inertia.get(
                this.$route('orders.incoming.index', {
                    page,
                    ...this.filters,
                    ...filters,
                }),
                {},
                {
                    only: ['incomingOrders', 'filters'],
                    replace: false,
                    preserveState: true,
                    preserveScroll: true,
                    onBefore: () => (this.isLoading = true),
                    onFinish: () => (this.isLoading = false),
                    onSuccess: () => {
                        this.orders = [
                            ...this.orders,
                            ...this.incomingOrders.data,
                        ]
                        window.history.replaceState(
                            {},
                            this.$page.title,
                            this.initialUrl,
                        )
                    },
                },
            )
        },
    },
}
</script>
<style lang="scss">
#listContainer {
    max-height: 77vh;
    overflow: auto;

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
