<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import EditCatalogItemForm from '@/Pages/Catalog/Components/EditCatalogItemForm.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
// import OrderListenerMixin from '../Mixins/OrderListenerMixin'
import JumboUploadButton from '../Files/Components/JumboUploadButton.vue'
import OrderProductsForm from './Components/OrderProductsForm.vue'
import CatalogItem from '@/Pages/Catalog/Components/CatalogItem.vue'
import { router } from '@inertiajs/vue2'
import CatalogItemDetailsModal from '@/Pages/Catalog/Components/CatalogItemDetailsModal.vue'
import { computed, ref } from 'vue'

const props = defineProps({
    order: {
        required: true,
        type: Object,
    },

    products: {
        required: true,
        type: Array,
    },

    totalPrice: {
        required: true,
        type: Number,
    },

    permissions: {
        type: Object,
        required: true,
    },
})

const { $route, $t, $confirm, $root } = useGlobalVariables()

const editCatalogItemFormRef = ref(null)
const orderProductFormRef = ref(null)
const catalogItemDetailsModal = ref(null)

const authCanUpdateProducts = computed(
    () => props.permissions?.can_update_used_products,
)
const authIsContractor = computed(() => props.permissions?.is_contractor)
const headers = computed(() => {
    let action = []

    if (authCanUpdateProducts.value) {
        action = [
            {
                text: $t('labels.actions'),
                value: 'action',
                sortable: false,
                align: 'center',
            },
        ]
    }

    return [
        {
            text: $t('catalog.labels.item.image'),
            value: 'image',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.item-name'),
            value: 'name',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.description'),
            value: 'description',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.type'),
            value: 'type',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.internal-id'),
            value: 'internal_id',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.vendor-id'),
            value: 'vendor_id',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.manufacture-id'),
            value: 'manufacture_id',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.unit'),
            value: 'unit',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.purchasing-price'),
            value: 'purchasing_price',
            sortable: false,
        },
        {
            text: $t('catalog.labels.item.selling-price'),
            value: 'selling_price',
            sortable: false,
        },
        {
            text: $t('order.products.quantity'),
            value: 'quantity',
            sortable: false,
            align: 'center',
        },
        ...action,
        {
            text: $t('labels.status'),
            value: 'status',
            sortable: false,
            align: 'center',
        },
        {
            text: $t('labels.invoice-status'),
            value: 'status',
            sortable: false,
            align: 'center',
        },
    ]
})

function removingProduct(orderProduct) {
    const order = props.order

    $confirm({
        title: $t('labels.delete'),
        question: $t('order.products.confirm-delete-product'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('orders.show.products.destroy', {
                    order,
                    orderProduct,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            )
        },
    })
}

function updateQuantity(orderProduct, quantity) {
    const order = props.order

    router.patch(
        $route('orders.show.products.update', {
            order,
            orderProduct,
        }),
        {
            quantity,
        },
        {
            only: ['errors', 'totalPrice', 'products'],
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}

function goToInvoice(invoice) {
    if (!authIsContractor.value) return

    const order = props.order
    window
        .open(
            $route('orders.show.invoices.show', {
                order,
                invoice,
            }),
            '_blank',
        )
        .focus()
}
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
                    {{ $t('order.products.order-products') }}
                </v-toolbar-title>

                <v-spacer />
            </v-toolbar>
        </portal>
        <JumboUploadButton
            v-if="!products.length"
            :title="$t('order.products.add-product')"
            icon="add_shopping_cart"
            @click:card="(e) => orderProductFormRef.show()"
        />
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
                <v-flex xs12>
                    <v-data-table
                        :headers="headers"
                        :items="products"
                        :total-items="products.length"
                        :hide-actions="true"
                        class="elevation-1 px-0 py-2"
                    >
                        <template #items="{ item }">
                            <CatalogItem
                                :item="item.details"
                                :can-delete-item="
                                    (!item?.is_paid && authCanUpdateProducts) ||
                                    !item?.is_delivery_ticket_materials
                                "
                                :can-update-item="false"
                                :can-update-quantity="
                                    !item?.is_paid && authCanUpdateProducts
                                "
                                :with-quantities-column="true"
                                :quantity="item.quantity"
                                :class="{
                                    'tw-bg-green-50': item?.is_paid,
                                }"
                                :hide-actions="!authCanUpdateProducts"
                                @click:delete="() => removingProduct(item)"
                                @click:add="
                                    () =>
                                        updateQuantity(
                                            item,
                                            parseInt(item.quantity) + 1,
                                        )
                                "
                                @click:minus="
                                    () =>
                                        updateQuantity(
                                            item,
                                            parseInt(item.quantity) - 1,
                                        )
                                "
                                @click:show-details="
                                    (item) => catalogItemDetailsModal.show(item)
                                "
                                @click:edit-quantity="
                                    () => editCatalogItemFormRef.show(item)
                                "
                            >
                                <template #additional>
                                    <td>
                                        <v-chip
                                            :color="
                                                item.is_delivery_ticket_materials
                                                    ? 'success'
                                                    : 'info'
                                            "
                                        >
                                            {{ item.status_label }}
                                        </v-chip>
                                    </td>
                                    <td>
                                        <v-chip
                                            :class="{
                                                clickable: authIsContractor,
                                            }"
                                            :color="item.invoice.status.color"
                                            @click="goToInvoice(item.invoice)"
                                        >
                                            {{ item.invoice.status.label }}
                                        </v-chip>
                                    </td>
                                </template>
                            </CatalogItem>
                        </template>
                    </v-data-table>
                </v-flex>
                <v-flex xs12>
                    <div class="tw-font-bold tw-text-right">
                        Total: {{ totalPrice }}
                    </div>
                </v-flex>
            </v-layout>
        </v-container>

        <v-btn
            color="primary"
            dark
            fixed
            bottom
            right
            icon
            large
            @click="(e) => orderProductFormRef.show()"
        >
            <v-icon>add</v-icon>
        </v-btn>

        <OrderProductsForm
            ref="orderProductFormRef"
            :title="$t('order.products.add-product-to-this-order')"
            :route="$route('orders.show.products.store', { order })"
        />

        <CatalogItemDetailsModal ref="catalogItemDetailsModal" />

        <EditCatalogItemForm
            ref="editCatalogItemFormRef"
            :order="order"
        />
    </div>
</template>
