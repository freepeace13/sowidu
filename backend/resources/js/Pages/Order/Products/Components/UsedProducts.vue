<script setup>
import SubmitButton from '@components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import CatalogItem from '@/Pages/Catalog/Components/CatalogItem.vue'
import { useForm } from '@inertiajs/vue2'
import axios from 'axios'
import { reactive, ref, watch } from 'vue'
import { toRef } from 'vue'
import { watchDebounced } from '@vueuse/core'

const emit = defineEmits(['close'])

defineExpose({ show })

const componentProps = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
    activeTab: {
        required: true,
        type: String,
    },
})

const { $t, $route } = useGlobalVariables()
const activeTab = toRef(componentProps, 'activeTab')

const form = useForm({
    products: [],
})
const filters = reactive({
    q: null,
    type: null,
})
const pagination = ref({
    page: 1,
})
const products = ref([])
const isFetching = ref(false)

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
]

const itemTypes = getPageProps('itemTypeOptions', [])

watch(activeTab, async (tab) => {
    if (tab != 'used_products') {
        return
    }

    await reset()
    await fetchItems()
})

watchDebounced(filters, async () => {
    await fetchItems()
})

async function reset() {
    products.value = []
    form.clearErrors()
    form.reset()
    filters.q = null
    filters.type = null
}

function close() {
    reset()
    emit('close')
}

function show() {
    form.reset()
    fetchItems()
}

async function fetchItems(page = 1) {
    try {
        products.value = []
        isFetching.value = true

        const params = {
            ...filters,
            type: filters.type?.value,
            page,
        }

        const {
            data: { data, per_page, current_page, total },
        } = await axios.get($route('json.catalog.items.index', params))

        if (page == 1) {
            products.value = data
        } else {
            products.value.push(...data)
        }

        pagination.value = {
            rowsPerPage: per_page,
            page: current_page,
            totalItems: total,
        }
    } catch (error) {
        console.error('error:', error)
    } finally {
        isFetching.value = false
    }
}

async function submit() {
    form.transform((data) => ({
        ...data,
        products: data.products.map((product) => product.id),
    })).post(componentProps.endpoint, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset()
        },
    })
}
</script>
<template>
    <div>
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
                        <v-combobox
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
                            v-model="form.products"
                            :headers="headers"
                            :items="products"
                            :loading="isFetching"
                            :pagination.sync="pagination"
                            :total-items="pagination.totalItems"
                            :rows-per-page-items="[
                                pagination?.rowsPerPage ?? 10,
                            ]"
                            select-all
                            item-key="id"
                            class="elevation-1 px-0 py-2 items-list"
                            @update:pagination="({ page }) => fetchItems(page)"
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
                @click="close"
            >
                {{ $t('buttons.cancel') }}
            </v-btn>
            <SubmitButton
                :loading="form.processing"
                :disabled="form.processing || !form.products.length"
                @click="submit"
            >
                <!-- :disabled="form.processing || !form.products.length" -->
                {{ $t('catalog.buttons.add-to-order') }}
            </SubmitButton>
        </v-card-actions>
    </div>
</template>
<style lang="scss" scoped>
.items-list {
    max-height: 460px;
    min-height: 460px;
    overflow: auto;
}
</style>
