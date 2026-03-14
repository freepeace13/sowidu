<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { computed } from 'vue'

const props = defineProps({
    item: {
        required: true,
        type: Object,
    },
    isUpdatingQuantity: {
        required: false,
        type: Boolean,
        default: false,
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
    isExpanded: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const { $t } = useGlobalVariables()

const emit = defineEmits([
    'click:remove',
    'click:update-quantity',
    'click:show-more',
    'click:update-rate',
    'click:edit-quantity-price',
])

const quantity = computed(() => props.item.quantity_formatted)

const isDeliveryTicket = computed(() => props.item?.is_delivery_ticket)
const isProduct = computed(() => !isDeliveryTicket.value)
const isWorkLog = computed(() => props.item?.is_work_log)

const itemQuantityIsEditable = computed(
    () => props.item?.actions.editable_quantity ?? true,
)

const itemIsDeletable = computed(() => props.item?.actions.deletable ?? true)
const itemPriceIsEditable = computed(
    () => props.item?.actions.editable_price ?? false,
)
const unitName = computed(() => {
    const item = props.item
    if (item?.is_work_log) {
        return $t('labels.hour')
    }

    return item?.unit_name ?? item?.details?.unit_name ?? '--'
})

function editPrice(item) {
    if (item.is_work_log) {
        return emit('click:update-rate', item)
    }

    return emit('click:edit-quantity-price', item)
}
</script>
<template>
    <tr
        :class="[
            'tw-align-text-top tw-mt-1',
            {
                'delivery-ticket-material': item.is_delivery_ticket_materials,
                'tw-bg-teal-50': isDeliveryTicket,
            },
        ]"
    >
        <td>
            <div
                class="tw-flex tw-justify-between tw-items-start tw-align-top tw-gap-x-2 mt-2"
            >
                <div class="tw-flex tw-flex-col tw-items-start tw-w-[80%]">
                    <div class="!tw-font-semibold tw-text-left">
                        {{ item?.name }}
                    </div>
                    <div
                        :class="[
                            'tw-leading-normal',
                            'tw-text-left',
                            'tw-text-xs',
                            'tw-w-[380px]',
                            {
                                '!tw-max-h-[18px] !tw-overflow-hidden':
                                    !isExpanded,
                                '!tw-max-h-full tw-mb-3': isExpanded,
                            },
                        ]"
                    >
                        <div
                            :class="{
                                'text-truncate': !isExpanded,
                            }"
                        >
                            {{ item?.description }}
                        </div>
                    </div>
                </div>
                <div
                    class="info--text hover:tw-underline tw-cursor-pointer tw-whitespace-nowrap tw-self-center"
                    @click="$emit('click:show-more')"
                >
                    {{
                        isExpanded
                            ? $t('labels.show-less')
                            : $t('labels.show-more')
                    }}
                </div>
            </div>
        </td>
        <td>
            <div
                v-if="isProduct"
                class="tw-flex tw-items-center tw-justify-center"
            >
                <v-btn
                    v-if="showUpdateQuantity && itemQuantityIsEditable"
                    v-tooltip="$t('catalog.buttons.reduce-quantity')"
                    flat
                    icon
                    color="orange"
                    small
                    :loading="isUpdatingQuantity"
                    :disabled="quantity == 1 || isUpdatingQuantity"
                    @click="
                        $emit('click:update-quantity', {
                            item,
                            quantity: parseFloat(quantity) - 1,
                        })
                    "
                >
                    <v-icon small>remove_circle_outline</v-icon>
                </v-btn>
                <b
                    v-tooltip="$t('catalog.buttons.edit-quantity')"
                    class="tw-cursor-pointer"
                    @click="$emit('click:edit-quantity-price', item)"
                >
                    {{ quantity }}
                </b>
                <v-btn
                    v-if="showUpdateQuantity && itemQuantityIsEditable"
                    v-tooltip="$t('catalog.buttons.add-quantity')"
                    flat
                    icon
                    color="green"
                    small
                    :disabled="isUpdatingQuantity"
                    :loading="isUpdatingQuantity"
                    @click="
                        $emit('click:update-quantity', {
                            item,
                            quantity: parseFloat(quantity) + 1,
                        })
                    "
                >
                    <v-icon small>add_circle_outline</v-icon>
                </v-btn>
            </div>
        </td>
        <td>
            <div>
                {{ unitName }}
            </div>
        </td>
        <td class="tw-relative tw-whitespace-nowrap">
            <div>
                {{ item.price_formatted }}
                <v-btn
                    v-if="showUpdateQuantity && itemPriceIsEditable"
                    v-tooltip="
                        `${
                            isWorkLog
                                ? $t('invoices.buttons.change-rate')
                                : $t('catalog.buttons.edit-price')
                        }`
                    "
                    class="!tw-absolute tw-right-[-12px] tw-top-1"
                    flat
                    icon
                    color="info"
                    small
                    @click="editPrice(item)"
                >
                    <v-icon small>edit</v-icon>
                </v-btn>
            </div>
        </td>
        <td class="tw-relative tw-whitespace-nowrap">
            <div class="tw-font-medium">
                {{ item.subtotal_formatted }}
            </div>
        </td>
        <td v-if="showActions && itemIsDeletable">
            <v-btn
                v-tooltip="$t('buttons.remove')"
                small
                flat
                class="tw-my-0"
                icon
                color="error"
                @click="$emit('click:remove', item)"
            >
                <v-icon small>cancel</v-icon>
            </v-btn>
        </td>
    </tr>
</template>
<style scoped>
.delivery-ticket-material {
    .material-name {
        @apply tw-ml-5;
    }
}
</style>
