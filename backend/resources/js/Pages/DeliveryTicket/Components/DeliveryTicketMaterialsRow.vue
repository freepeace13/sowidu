<script setup>
import { authCan } from '@/Composables/useAuth'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import CatalogItemDetailsModal from '@/Pages/Catalog/Components/CatalogItemDetailsModal.vue'
import { router, useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'
import EditMaterialPricesForm from './EditMaterialPricesForm.vue'

const props = defineProps({
    deliveryTicket: {
        required: true,
        type: Object,
    },
    materials: {
        required: true,
        type: Array,
    },
    totals: {
        required: false,
        type: Object,
        default: () => ({
            purchasing_price: 0,
            selling_price: 0,
        }),
    },
})

const { $t, $route, $confirm } = useGlobalVariables()
const catalogItemDetailsModal = ref(null)
const editMaterialPricesForm = ref(null)
const form = useForm({
    quantity: props.deliveryTicket?.quantity ?? 1,
})

const headers = [
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
        text: $t('catalog.labels.item.vendor-id'),
        value: 'vendor_id',
        sortable: false,
    },
    {
        text: $t('delivery_tickets.labels.quantity'),
        value: 'quantity',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('delivery_tickets.labels.purchasing-price'),
        value: 'purchasing_price',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('delivery_tickets.labels.selling-price'),
        value: 'selling_price',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('labels.status'),
        value: 'action',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('labels.actions'),
        value: 'action',
        sortable: false,
        align: 'center',
        width: '50px',
    },
]

const authCanEditPrices = computed(() => authCan('manage_materials'))

function confirmRemoving(material) {
    const deliveryTicket = props.deliveryTicket

    $confirm({
        title: $t('labels.delete'),
        question: $t('delivery_tickets.message.confirm_material_removing'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('delivery_tickets.materials.destroy', {
                    material,
                    deliveryTicket,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['materials', 'totals'],
                },
            )
        },
    })
}

function updateQuantity(material, quantity) {
    const deliveryTicket = props.deliveryTicket
    form.quantity = quantity

    form.patch(
        $route('delivery_tickets.materials.update', {
            deliveryTicket,
            material,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['materials', 'totals'],
        },
    )
}
</script>
<template>
    <v-card flat>
        <EditMaterialPricesForm
            ref="editMaterialPricesForm"
            :delivery-ticket="deliveryTicket"
        />
        <CatalogItemDetailsModal ref="catalogItemDetailsModal" />
        <v-card-text class="px-0 pt-0">
            <v-alert
                :value="!materials.length"
                color="info"
                icon="info"
                outline
                class="pa-2"
            >
                <div class="!tw-flex !tw-items-center tw-justify-between">
                    <div>
                        {{ $t('delivery_tickets.message.no-materials') }}
                    </div>
                    <v-btn
                        color="info"
                        small
                        @click="$emit('click:add-materials', deliveryTicket)"
                    >
                        {{ $t('delivery_tickets.buttons.add-materials') }}
                    </v-btn>
                </div>
            </v-alert>

            <v-data-table
                v-show="materials.length"
                :headers="headers"
                :items="materials"
                :total-items="materials.length"
                :hide-actions="true"
                class="elevation-2 px-0 py-2 dense-header ticket-materials-table"
            >
                <template
                    #items="{
                        item: { details: item, id, quantity, is_paid },
                        item: material,
                    }"
                >
                    <tr>
                        <td>
                            <v-avatar
                                tile
                                :size="$vuetify.breakpoint.xs ? 26 : 30"
                                class="mr-2"
                            >
                                <v-img
                                    v-if="media"
                                    :size="30"
                                    :src="item.media.thumbnail_url"
                                    :alt="item.name"
                                    :lazy-src="item.media.thumbnail_url"
                                />
                            </v-avatar>
                        </td>
                        <td class="!tw-font-semibold">
                            <div
                                class="info--text tw-cursor-pointer hover:tw-underline"
                                @click="
                                    () => catalogItemDetailsModal.show(item)
                                "
                            >
                                {{ item.name }}
                            </div>
                        </td>
                        <td>
                            <div
                                class="text-truncate tw-w-[270px] tw-text-left tw-text-sm"
                            >
                                {{
                                    item.description ?? item?.short_description
                                }}
                            </div>
                        </td>
                        <td class="!tw-text-sm px-0">{{ item.type.name }}</td>
                        <td class="!tw-text-xs">{{ item.vendor_id }}</td>
                        <td>
                            <div
                                class="tw-flex tw-items-center tw-justify-center"
                            >
                                <v-btn
                                    v-tooltip="
                                        $t('catalog.buttons.reduce-quantity')
                                    "
                                    flat
                                    icon
                                    color="orange"
                                    small
                                    class="mx-0"
                                    :loading="form.processing"
                                    :disabled="quantity == 1 || form.processing"
                                    @click="updateQuantity(id, quantity - 1)"
                                >
                                    <v-icon small>remove_circle_outline</v-icon>
                                </v-btn>
                                <input
                                    type="number"
                                    class="tw-text-center tw-appearance-none !tw-w-16 tw-p-1.5"
                                    :value="quantity"
                                    @change="
                                        updateQuantity(id, $event.target.value)
                                    "
                                />
                                <v-btn
                                    v-tooltip="
                                        $t('catalog.buttons.add-quantity')
                                    "
                                    flat
                                    icon
                                    color="green"
                                    small
                                    class="mx-0"
                                    :disabled="form.processing"
                                    :loading="form.processing"
                                    @click="updateQuantity(id, quantity + 1)"
                                >
                                    <v-icon small>add_circle_outline</v-icon>
                                </v-btn>
                            </div>
                        </td>
                        <td class="tw-text-center !tw-relative">
                            {{ material.purchasing_price_formatted }}
                            <v-btn
                                v-if="authCanEditPrices"
                                class="!tw-absolute tw-top-1 mr-0 ml-2"
                                flat
                                icon
                                color="info"
                                small
                                @click="editMaterialPricesForm.show(material)"
                            >
                                <v-icon size="12">edit</v-icon>
                            </v-btn>
                        </td>
                        <td class="tw-text-center !tw-relative">
                            {{ material.selling_price_formatted }}
                            <v-btn
                                v-if="authCanEditPrices"
                                class="!tw-absolute tw-top-1 mr-0 ml-2"
                                flat
                                icon
                                color="info"
                                small
                                @click="editMaterialPricesForm.show(material)"
                            >
                                <v-icon size="12">edit</v-icon>
                            </v-btn>
                        </td>

                        <td>
                            <v-chip
                                :color="is_paid ? '#4caf50' : ''"
                                small
                            >
                                {{
                                    is_paid
                                        ? $t('labels.paid')
                                        : $t('labels.unpaid')
                                }}
                            </v-chip>
                        </td>
                        <td class="px-0 tw-text-center">
                            <v-btn
                                v-tooltip="$t('buttons.remove')"
                                small
                                flat
                                class="tw-my-0 mx-0"
                                icon
                                color="error"
                                @click="confirmRemoving(id)"
                            >
                                <v-icon small>cancel</v-icon>
                            </v-btn>
                        </td>
                    </tr>
                </template>
                <template #footer>
                    <tr class="tw-text-sm tw-text-center">
                        <td :colspan="headers.length - 5" />
                        <td class="tw-text-right tw-font-semibold">
                            {{ $t('labels.total') }}
                        </td>
                        <td>{{ totals.purchasing_price_formatted }}</td>
                        <td>{{ totals.selling_price_formatted }}</td>
                    </tr>
                </template>
            </v-data-table>
        </v-card-text>
    </v-card>
</template>
<style lang="scss">
.ticket-materials-table {
    .v-table__overflow {
        @apply md:tw-overflow-x-hidden;
    }

    table {
        thead tr {
            height: 40px;
        }

        tr {
            th {
                height: 36px;

                &:first-child {
                    @apply tw-pr-0;
                }

                &:last-child {
                    width: 30px;
                    @apply tw-px-0 tw-pr-3;
                }

                &:nth-child(4) {
                    @apply tw-px-0;
                }

                &:nth-child(5) {
                    @apply tw-pr-2;
                }
            }

            td {
                &:first-child {
                    @apply tw-pr-0;
                }
            }
        }
    }
}
</style>
