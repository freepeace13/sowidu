<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { isNull } from '@/Composables/useUtils'
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'

const props = defineProps({
    value: {
        type: Object,
        required: false,
        default: null,
    },
    inputItems: {
        required: false,
        type: Array,
    },
})

const emit = defineEmits(['input', 'update:search'])
const { $route } = useGlobalVariables()

const selected = ref(props.value)
const search = ref(null)
const items = ref([])
const isLoading = ref(false)
const hasPropInputItems = computed(() => props.inputItems !== undefined)

watch(
    () => props.inputItems,
    (newItems) => {
        // Only update if the prop is defined
        if (newItems !== undefined) {
            items.value = newItems
        }
    },
    { immediate: true },
)

const fetch = useDebounceFn(async (params) => {
    if (hasPropInputItems.value) {
        emit('update:search', params?.q)
        return
    }

    if (!params?.q) return

    try {
        items.value = []
        isLoading.value = true
        const { data } = await axios.get(
            $route('json.autocomplete.orders', params),
        )
        items.value = data
    } catch (error) {
        console.error(error)
    } finally {
        isLoading.value = false
    }
}, 500)

onMounted(() => {
    if (!isNull(props.value)) {
        setSelectedValue(props.value)
    }

    // Only fetch items if we don't have inputItems provided
    if (!hasPropInputItems.value) {
        fetch({ q: null })
        initItems()
    }
})

function setSelectedValue(value) {
    items.value.push(value)
    selected.value = value
}

async function initItems() {
    items.value = []
    const { data } = await axios.get(
        $route('json.autocomplete.orders', {
            orderBy: 'created_at',
        }),
    )
    items.value = data
}
</script>
<template>
    <v-autocomplete
        v-model="selected"
        v-bind="$attrs"
        :search-input.sync="search"
        :loading="isLoading"
        :items="items"
        :no-filter="true"
        prepend-inner-icon="shopping_cart"
        outline
        single-line
        allow-overflow
        hide-selected
        return-object
        color="primary"
        item-text="name"
        @update:searchInput="(q) => fetch({ q })"
        @input="(val) => $emit('input', val)"
    >
        <template #selection="data">
            <slot
                name="selection"
                v-bind="data"
            >
                <v-list-tile-avatar>
                    <img :src="data?.item?.client?.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title
                        class="tw-flex tw-items-end tw-justify-between tw-font-semibold"
                    >
                        {{ data?.item?.order_number }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="!tw-text-xs tw-font-thin">
                        {{ data?.item?.client?.name }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </slot>
        </template>
        <template #item="{ item, item: { client, delivery_address } }">
            <slot
                name="item"
                v-bind="item"
            >
                <v-list-tile-avatar>
                    <img :src="client?.photo" />
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title
                        class="tw-flex tw-items-end tw-justify-between"
                    >
                        <div class="">
                            {{ client.name }}
                        </div>
                        <div class="tw-font-semibold">
                            {{ item.order_number }}
                        </div>
                    </v-list-tile-title>
                    <v-list-tile-sub-title class="!tw-text-xs tw-font-thin">
                        {{ delivery_address.full }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </slot>
        </template>
        <template #no-data>
            <slot name="no-data">
                <v-list-tile
                    avatar
                    ripple
                >
                    <v-list-tile-avatar />
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ $t('hints.nothing-found') }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </slot>
        </template>
    </v-autocomplete>
</template>
