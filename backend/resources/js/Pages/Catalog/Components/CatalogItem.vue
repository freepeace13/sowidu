<script setup>
import { getPageProps } from '@/Composables/useUtils'
import { computed } from 'vue'

defineProps({
    item: {
        required: true,
        type: Object,
    },
    canDeleteItem: {
        required: false,
        type: Boolean,
        default: false,
    },
    canUpdateItem: {
        required: false,
        type: Boolean,
        default: false,
    },
    canUpdateQuantity: {
        required: false,
        type: Boolean,
        default: true,
    },
    hideActions: {
        required: false,
        type: Boolean,
        default: false,
    },
    selectable: {
        required: false,
        type: Boolean,
        default: false,
    },
    withQuantitiesColumn: {
        required: false,
        type: Boolean,
        default: false,
    },
    quantity: {
        required: false,
        type: Number,
        default: 0,
    },
    currency: {
        type: Object,
        required: false,
        default: () => ({
            name: null,
            symbol: null,
        }),
    },
    isSelected: {
        required: false,
        type: Boolean,
        default: false,
    },
})

defineEmits([
    'click:edit-quantity',
    'click:minus',
    'click:add',
    'click:edit',
    'click:delete',
    'click:show-details',
    'click:row',
])

const defaultCurrency = computed(() => getPageProps('defaults.currency.symbol'))
</script>
<template>
    <tr
        :active="isSelected"
        @click="() => $emit('click:row')"
    >
        <slot name="select" />

        <slot name="additional_start" />

        <td>
            <v-avatar
                tile
                :size="$vuetify.breakpoint.xs ? 26 : 30"
                class="mr-2"
            >
                <v-img
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
                @click="$emit('click:show-details', item)"
            >
                {{ item.name }}
            </div>
        </td>
        <td>
            <div class="text-truncate tw-w-[270px] tw-text-left">
                {{ item.description ?? item?.short_description }}
            </div>
        </td>
        <td>{{ item.type.name }}</td>
        <td>{{ item.internal_id }}</td>
        <td>{{ item.vendor_id }}</td>
        <td>{{ item.manufacture_id }}</td>
        <td>{{ item?.unit_name ?? '--' }}</td>
        <td class="!tw-text-center">
            {{ item?.purchasing_price ?? '--' }}
            <span v-show="item?.purchasing_price">
                {{ currency?.symbol ?? defaultCurrency }}</span
            >
        </td>
        <td class="!tw-text-center">
            {{ item.selling_price }} {{ currency?.symbol ?? defaultCurrency }}
        </td>
        <td v-if="withQuantitiesColumn">
            <div class="tw-flex tw-items-center tw-justify-center">
                <v-btn
                    v-show="canUpdateQuantity"
                    v-tooltip="$t('catalog.buttons.reduce-quantity')"
                    flat
                    icon
                    color="orange"
                    small
                    :disabled="quantity == 1"
                    @click="$emit('click:minus', item)"
                >
                    <v-icon small>remove_circle_outline</v-icon>
                </v-btn>
                <b
                    class="tw-cursor-pointer"
                    @click="$emit('click:edit-quantity')"
                >
                    {{ quantity }}
                </b>
                <v-btn
                    v-show="canUpdateQuantity"
                    v-tooltip="$t('catalog.buttons.add-quantity')"
                    flat
                    icon
                    color="green"
                    small
                    @click="$emit('click:add', item)"
                >
                    <v-icon small>add_circle_outline</v-icon>
                </v-btn>
            </div>
        </td>
        <td
            v-if="!hideActions"
            class="tw-flex tw-items-center"
        >
            <v-btn
                v-show="canUpdateItem"
                v-tooltip="$t('buttons.update')"
                flat
                icon
                color="info"
                small
                @click="$emit('click:edit', item)"
            >
                <v-icon small>edit</v-icon>
            </v-btn>
            <v-btn
                v-if="canDeleteItem"
                v-tooltip="$t('buttons.delete')"
                flat
                icon
                small
                color="error"
                @click="$emit('click:delete', item)"
            >
                <v-icon small>delete</v-icon>
            </v-btn>
        </td>
        <slot name="additional" />
    </tr>
</template>
