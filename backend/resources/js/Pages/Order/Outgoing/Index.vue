<script>
export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
// TODO: Delete this file
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { deny } from '@/Composables/usePermissionFilters'
import OrderRow from '@/Features/Order/Components/OrderRow.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import ItemActionMenu from '@components/Menus/ItemActionMenu.vue'
import { router } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/shared'
import { computed, reactive, ref, watch } from 'vue'
import { VFlex } from 'vuetify/lib'
import EditOrderForm from '../Components/EditOrderForm.vue'
import OrderToolbar from '../Components/OrderToolbar.vue'
import OrderLayout from '../OrderLayout.vue'

const OutgoingOrderForm = () =>
    import(
        /* webpackChunkName: 'order-outgoing-form' */ './Components/OutgoingOrderForm.vue'
    )

const props = defineProps({
    outgoingOrders: {
        required: true,
        type: Array,
    },
    filters: {
        required: true,
        type: [Object, Array],
        default: () => ({}),
    },
    ownedPlaces: {
        required: false,
        type: Array,
        default: () => [],
    },
    currentAddress: {
        required: false,
        type: Object,
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
})

const { $t, $route, $root, $confirm, app } = useGlobalVariables()
const $vuetify = app.proxy.$vuetify

const headers = [
    { text: 'Name', sortable: false },
    { text: 'Description', sortable: false },
    { text: 'Planned Start - Finished Date', sortable: false },
    { text: 'Status', sortable: false },
    { text: 'Actions', sortable: false, align: 'right' },
]

const isLoading = ref(false)
const pagination = reactive({
    page: 1,
    rowsPerPage: 10,
})

const outgoingOrderFormRef = ref(null)
const itemActionMenuRef = ref(null)
const editOrderFormRef = ref(null)

const total = computed(() => props.outgoingOrders.length)
const denyPermission = (user, permission) => deny(user, permission)

watch(
    pagination,
    ({ rowsPerPage, page }) => {
        router.reload({
            only: ['outgoingOrders', 'filters'],
            replace: false,
            preserveState: true,
            preserveScroll: true,
            data: {
                rowsPerPage,
                page,
            },
            onBefore: () => (isLoading.value = true),
            onFinish: () => (isLoading.value = false),
        })
    },
    { deep: true },
)

const filterOrders = useDebounceFn(async (q) => {
    router.reload({
        only: ['outgoingOrders', 'filters'],
        preserveState: true,
        preserveScroll: true,
        replace: false,
        data: {
            ...props.filters,
            q,
        },
        onBefore: () => (isLoading.value = true),
        onFinish: () => (isLoading.value = false),
    })
})

function destroy(order) {
    $confirm.ask({
        title: 'Delete',
        question: 'Do you want to delete this order?',
        type: 'delete',
        confirm: () => {
            router.delete($route('orders.incoming.destroy', { order }), {
                preserveState: true,
                preserveScroll: true,
                only: ['outgoingOrders'],
                onSuccess: () => {
                    $root.$emit('flash.success', 'Order has been deleted.')
                },
            })
        },
    })
}
</script>
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
                @search="(e) => filterOrders(e)"
            />
        </portal>

        <v-layout
            row
            wrap
            align-center
        >
            <VFlex>
                <v-btn
                    color="primary"
                    :disabled="denyPermission(user, 'can create order')"
                    :small="$vuetify.breakpoint.xsOnly"
                    @click="outgoingOrderFormRef?.show()"
                >
                    <v-icon class="mr-1">add</v-icon>
                    {{
                        $vuetify.breakpoint.xs
                            ? ''
                            : $tc('order.outgoing.buttons.create')
                    }}
                </v-btn>
            </VFlex>
            <v-spacer />
            <v-flex
                tw-text-right
                tw-font-bold
                tw-text-base
            >
                {{ total }}
                {{
                    `${$t('order.labels.outgoing')} ${$tc(
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
                    :headers="headers"
                    :headers-length="headers.length"
                    :items="outgoingOrders"
                    :loading="isLoading"
                    class="elevation-1"
                    hide-actions
                    no-data-text="No outgoing orders available."
                >
                    <template #items="{ item: { contractor, ...order }, item }">
                        <OrderRow
                            :order="order"
                            :from="contractor"
                            @click:more="
                                (e) => itemActionMenuRef?.show(e, item)
                            "
                        />
                    </template>
                </v-data-table>
            </v-flex>
        </v-layout>

        <OutgoingOrderForm
            ref="outgoingOrderFormRef"
            :owned-places="ownedPlaces"
            :current-address="currentAddress"
        />

        <ItemActionMenu
            ref="itemActionMenuRef"
            @click:update="(order) => editOrderFormRef?.show(order)"
            @click:delete="(order) => destroy(order)"
            @click:details="
                (order) => {
                    router.get(
                        $route('orders.show', {
                            order,
                        }),
                    )
                }
            "
        />

        <EditOrderForm
            ref="editOrderFormRef"
            route="orders.outgoing.update"
            :refresh-props="['outgoingOrders']"
        />
    </div>
</template>
