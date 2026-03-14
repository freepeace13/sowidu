<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { computed } from 'vue'

const props = defineProps({
    item: {
        required: true,
        type: Object,
    },
    editable: {
        required: false,
        type: Boolean,
        default: false,
    },
    isUpdatingQuantity: {
        type: Boolean,
        default: false,
    },
})

const { $t } = useGlobalVariables()

defineEmits(['click:remove', 'click:edit', 'click:update-quantity'])

const unitName = computed(() => props.item?.details?.unit_name ?? '--')
const quantity = computed(() => props.item.quantity)
</script>
<template>
    <tr class="tw-mt-1 tw-h-[55px]">
        <td>
            <div
                class="tw-flex tw-justify-between tw-items-start tw-align-top tw-gap-x-2 mt-2"
            >
                <div class="tw-flex tw-flex-col tw-items-start tw-w-[80%]">
                    <div class="!tw-font-semibold tw-text-left">
                        {{ item?.name }}
                    </div>
                    <div
                        class="tw-leading-normal tw-text-left tw-text-xs tw-w-[380px]"
                    >
                        <div>
                            {{ item?.description }}
                        </div>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <div class="tw-flex tw-items-center tw-justify-center">
                <v-btn
                    v-if="editable"
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
                    @click="$emit('click:edit', item)"
                >
                    {{ item.quantity_formatted }}
                </b>
                <v-btn
                    v-if="editable"
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
        <td class="px-1">
            <div>
                {{ unitName }}
            </div>
        </td>
        <td class="tw-relative tw-whitespace-nowrap">
            <div
                v-tooltip="$t('catalog.buttons.edit-price')"
                class="tw-cursor-pointer hover:tw-underline"
                @click="$emit('click:edit', item)"
            >
                {{ item.price_formatted }}
            </div>
            <v-icon
                v-if="editable"
                small
                color="info"
                class="hover:tw-underline tw-absolute tw-right-0 tw-transform tw-top-1/2 tw--translate-y-1/2"
                @click="$emit('click:edit', item)"
            >
                edit
            </v-icon>
        </td>
        <td class="tw-relative tw-whitespace-nowrap pr-1">
            <div class="tw-font-medium">
                {{ item.subtotal_formatted }}
            </div>
        </td>
        <td class="px-1">
            <v-btn
                v-if="editable"
                v-tooltip="$t('buttons.remove')"
                small
                flat
                class="tw-my-0 mx-0"
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
