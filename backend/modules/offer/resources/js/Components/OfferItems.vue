<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import OfferItem from './OfferItem.vue'
import EditOfferItem from './EditOfferItem.vue'
import { computed, ref } from 'vue'

const props = defineProps({
    offer: {
        type: Object,
    },
    items: {
        required: false,
        type: Array,
        default: () => [],
    },
    editable: {
        type: Boolean,
        default: false,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()
const editOfferItemRef = ref(null)

const form = useForm({
    quantity: 0,
})

const baseHeaders = [
    {
        text: $t('catalog.labels.item.item-name'),
        value: 'name',
        sortable: false,
        align: 'left',
        width: '40%',
    },
    {
        text: $t('offer.items.inputs.quantity'),
        value: 'quantity',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('catalog.labels.item.unit'),
        value: 'unit_name',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('offer.items.inputs.price'),
        value: 'price',
        sortable: false,
        align: 'center',
    },
    {
        text: $t('offer.items.inputs.total'),
        sortable: false,
        align: 'center',
    },
    {
        text: $t('labels.actions'),
        value: 'action',
        sortable: false,
        align: 'right',
    },
]

const headers = computed(() => {
    return props.editable ? baseHeaders : baseHeaders.slice(0, -1)
})

function confirmRemoving(item) {
    const offer = props.offer

    $confirm({
        title: $t('labels.delete'),
        question: $t('offer.messages.items.removing'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('offers.items.destroy', {
                    item,
                    offer,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['items', 'offer'],
                },
            )
        },
    })
}

function updateQuantity(item, quantity) {
    const offer = props.offer

    form.transform((data) => ({ ...data, price: item.price, quantity })).patch(
        $route('offers.items.update', {
            offer,
            item,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['items', 'offer'],
        },
    )
}
</script>
<template>
    <v-card flat>
        <EditOfferItem ref="editOfferItemRef" />
        <v-card-text class="pa-0">
            <v-data-table
                v-show="items.length"
                :headers="headers"
                :items="items"
                :hide-actions="true"
                :no-data-text="$t('invoices.message.items.empty')"
                item-key="id"
                class="elevation-2 px-0 dense-header columns-center offer-items-table"
            >
                <template #items="itemProps">
                    <OfferItem
                        :key="itemProps.item.id"
                        :item="itemProps.item"
                        :editable="editable"
                        :is-updating-quantity="form.processing"
                        @click:update-quantity="
                            ({ item, quantity }) =>
                                updateQuantity(item, quantity)
                        "
                        @click:remove="(item) => confirmRemoving(item)"
                        @click:edit="(item) => editOfferItemRef.show(item)"
                    />
                </template>
                <template #footer>
                    <slot name="totals" />
                </template>
                <!-- <template #expand="{ item: { details }, item }">
                    <v-card flat>
                        <InvoiceItemDetails
                            :details="details"
                            :item="item"
                        />
                    </v-card>
                </template> -->
            </v-data-table>
        </v-card-text>
    </v-card>
</template>
<style scoped>
::v-deep(.offer-items-table tfoot tr) {
    @apply tw-text-sm;
}
::v-deep(.spacer-row) {
    @apply tw-h-2;
}
</style>
