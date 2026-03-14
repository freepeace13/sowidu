<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import CatalogItem from '@/Pages/Catalog/Components/CatalogItem.vue'
import { useForm, usePage } from '@inertiajs/vue2'
import { useDebounceFn, watchDebounced } from '@vueuse/core'
import axios from 'axios'
import { toRef } from 'vue'
import { watch } from 'vue'
import { onMounted } from 'vue'
import { reactive } from 'vue'
import { ref } from 'vue'
import { getCurrentInstance } from 'vue'

const catalogProps = defineProps({
    activeTab: {
        required: true,
        type: String,
    },
    tabName: {
        required: true,
        type: String,
    },
})

defineEmits(['close'])

const app = getCurrentInstance()
const items = ref([])
const activeTab = toRef(catalogProps, 'activeTab')
const form = useForm({
    catalog_items: [],
})

const filters = reactive({
    q: null,
    type: null,
})

const isFetching = ref(false)
const pagination = ref({
    page: 1,
    descending: false,
    rowsPerPage: 10,
    sortBy: null,
    totalItems: 0,
})

const itemTypes = usePage().props.itemTypeOptions

watch(activeTab, (tab) => {
    if (tab != catalogProps.tabName) {
        return
    }

    reset()
    fetchItems()
})

watchDebounced(filters, () => {
    fetchItems()
})

onMounted(() => {
    reset()
    fetchItems()
})

function reset() {
    form.reset()
    form.clearErrors()

    filters.q = null
    filters.type = null
}

const fetchItems = useDebounceFn(async () => {
    isFetching.value = true

    const { page } = pagination.value

    const params = { ...filters, page }

    const {
        data: { data, per_page, current_page, total },
    } = await axios.get(window.route('json.catalog.items.index', params))

    pagination.value = {
        rowsPerPage: per_page,
        page: current_page,
        totalItems: total,
        descending: false,
        sortBy: null,
    }

    items.value = data
    isFetching.value = false
}, 500)

function paginate(payload) {
    pagination.value = payload
    fetchItems()
}

function submit() {
    const invoice = usePage().props.invoice

    form.transform((data) => ({
        ...data,
        catalog_items: data.catalog_items.map(({ id }) => id),
    })).post(window.route('invoices.items.store', { invoice }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
        only: ['items', 'totalPrice', 'flash', 'invoiceSummary'],
        onError: (errors) => app.proxy.$root.$emit('flash.validation', errors),
    })
}
</script>
<template>
    <v-card flat>
        <v-card-text>
            <v-container
                grid-list-lg
                fluid
                pa-2
            >
                <v-layout
                    row
                    wrap
                >
                    <v-flex
                        xs12
                        sm7
                    >
                        <v-text-field
                            v-model="filters.q"
                            color="primary"
                            :loading="isFetching"
                            :label="$t('labels.search')"
                            outline
                            :hide-details="true"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        sm5
                    >
                        <v-select
                            v-model="filters.type"
                            outline
                            no-filter
                            full-width
                            hide-no-data
                            append-icon="none"
                            :items="itemTypes"
                            :loading="isFetching"
                            :disabled="isFetching"
                            :hide-details="true"
                            :label="$t('labels.inputs.filter-by-type')"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        class="tw-flex tw-flex-col tw-gap-y-3"
                    >
                        <v-data-table
                            v-model="form.catalog_items"
                            :headers="[
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
                                    text: $t(
                                        'catalog.labels.item.manufacture-id',
                                    ),
                                    value: 'manufacture_id',
                                    sortable: false,
                                },
                                {
                                    text: $t('catalog.labels.item.unit'),
                                    value: 'unit',
                                    sortable: false,
                                },
                                {
                                    text: $t(
                                        'catalog.labels.item.purchasing-price',
                                    ),
                                    value: 'purchasing_price',
                                    sortable: false,
                                },
                                {
                                    text: $t(
                                        'catalog.labels.item.selling-price',
                                    ),
                                    value: 'selling_price',
                                    sortable: false,
                                },
                            ]"
                            :items="items"
                            :loading="isFetching"
                            :total-items="pagination.totalItems"
                            :rows-per-page-items="[
                                pagination?.rowsPerPage ?? 10,
                            ]"
                            select-all
                            item-key="id"
                            class="elevation-1 px-0 py-2"
                            @update:pagination="(payload) => paginate(payload)"
                        >
                            <template #items="props">
                                <CatalogItem
                                    :item="props.item"
                                    :hide-actions="true"
                                    :can-delete-item="false"
                                    :can-update-item="false"
                                    :selectable="true"
                                    :is-selected="props.selected"
                                    @click:row="
                                        () => (props.selected = !props.selected)
                                    "
                                >
                                    <template #select>
                                        <td>
                                            <v-checkbox
                                                v-model="props.selected"
                                                primary
                                                hide-details
                                            />
                                        </td>
                                    </template>
                                </CatalogItem>
                            </template>
                        </v-data-table>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card-text>
        <v-divider />
        <v-card-actions class="px-4 py-4">
            <v-spacer />
            <v-btn
                :disabled="form.processing"
                outline
                depressed
                @click="
                    () => {
                        reset()
                        $emit('close')
                    }
                "
            >
                {{ $t('buttons.close') }}
            </v-btn>
            <SubmitButton
                :loading="form.processing"
                :disabled="form.processing || !form.catalog_items.length"
                @click="submit"
            >
                {{ $t('invoices.buttons.add-to-invoice') }}
            </SubmitButton>
        </v-card-actions>
    </v-card>
</template>
