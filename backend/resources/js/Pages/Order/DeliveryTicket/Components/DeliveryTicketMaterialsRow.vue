<script setup>
import { router, useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { authCan } from '@/Composables/useAuth'
import CatalogItemDetailsModal from '@/Pages/Catalog/Components/CatalogItemDetailsModal.vue'
import EditMaterialPricesForm from '@/Pages/DeliveryTicket/Components/EditMaterialPricesForm.vue'
import { getPageProps } from '@/Composables/useUtils'

const props = defineProps({
    deliveryTicket: {
        required: true,
        type: Object,
    },
    cardTextClass: {
        required: false,
        type: String,
        default: null,
    },
    canUpdateMaterials: {
        required: false,
        type: Boolean,
        default: true,
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

const { $t, $confirm, $route } = useGlobalVariables()

const catalogItemDetailsRef = ref(null)
const editMaterialPricesForm = ref(null)

const materials = computed(() => getPageProps('materials'))
const form = useForm({
    quantity: props.deliveryTicket?.quantity ?? 1,
})
const canUpdateTicket = computed(() => authCan('can_update_ticket'))
const authCanUpdatePrices = computed(() => authCan('can_update_prices'))

const headers = [
    {
        text: $t('catalog.labels.item.image'),
        value: 'image',
        sortable: false,
    },
    {
        text: $t('catalog.labels.item.item-internal-id'),
        value: 'internal_id',
        sortable: false,
    },
    {
        text: $t('catalog.labels.item.article-number'),
        value: 'internal_id',
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
        <CatalogItemDetailsModal ref="catalogItemDetailsRef" />
        <v-card-text :class="['pt-0', cardTextClass]">
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
                        v-if="canUpdateMaterials"
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
                class="elevation-2 px-0 py-2 dense-header order-ticket-materials-table"
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
                                    v-if="item.media"
                                    :size="30"
                                    :src="item.media.thumbnail_url"
                                    :alt="item.name"
                                    :lazy-src="item.media.thumbnail_url"
                                />
                            </v-avatar>
                        </td>
                        <td class="!tw-font-semibold">
                            <div
                                class="info--text tw-cursor-pointer hover:tw-underline tw-text-left"
                                @click="() => catalogItemDetailsRef.show(item)"
                            >
                                {{ item.internal_id ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="!tw-font-semibold">
                            <div
                                class="info--text tw-cursor-pointer hover:tw-underline tw-text-left"
                                @click="() => catalogItemDetailsRef.show(item)"
                            >
                                {{ item.manufacture_id ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="!tw-font-semibold">
                            <div
                                class="info--text tw-cursor-pointer hover:tw-underline tw-text-left"
                                @click="() => catalogItemDetailsRef.show(item)"
                            >
                                {{ item.name }}
                            </div>
                        </td>
                        <td>
                            <div
                                class="text-truncate tw-w-[270px] tw-text-left tw-text-xs"
                            >
                                {{
                                    item.description ?? item?.short_description
                                }}
                            </div>
                        </td>
                        <td class="!tw-text-sm px-0 tw-text-center">
                            {{ item.type && item.type.name }}
                        </td>
                        <td class="!tw-text-xs !tw-text-left">
                            {{ item.vendor_id }}
                        </td>
                        <td>
                            <div class="tw-flex tw-items-center">
                                <v-btn
                                    v-if="canUpdateTicket"
                                    v-tooltip="
                                        $t('catalog.buttons.reduce-quantity')
                                    "
                                    flat
                                    icon
                                    color="orange"
                                    small
                                    :loading="form.processing"
                                    :disabled="quantity == 1 || form.processing"
                                    @click="updateQuantity(id, quantity - 1)"
                                >
                                    <v-icon small>remove_circle_outline</v-icon>
                                </v-btn>
                                <!-- <b>{{ quantity }}</b> -->
                                <input
                                    type="number"
                                    class="tw-text-center tw-appearance-none !tw-w-16 tw-p-1.5"
                                    :value="quantity"
                                    @change="
                                        updateQuantity(id, $event.target.value)
                                    "
                                />
                                <v-btn
                                    v-if="canUpdateTicket"
                                    v-tooltip="
                                        $t('catalog.buttons.add-quantity')
                                    "
                                    flat
                                    icon
                                    color="green"
                                    small
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
                                v-if="authCanUpdatePrices"
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
                                v-if="authCanUpdatePrices"
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
                                <div class="tw-text-xs">
                                    {{
                                        is_paid
                                            ? $t('labels.paid')
                                            : $t('labels.unpaid')
                                    }}
                                </div>
                            </v-chip>
                        </td>
                        <td v-if="canUpdateTicket">
                            <v-btn
                                v-tooltip="$t('buttons.remove')"
                                small
                                flat
                                class="tw-my-0"
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
.dense-header {
    table thead tr {
        height: 40px;

        th {
            height: 36px;
        }
    }
}

.order-ticket-materials-table {
    table {
        thead tr {
            height: 40px;

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
        }

        tbody tr {
            td {
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
        }
    }
}
</style>
