<script setup>
import ModalButtonClose from '@/Apps/Shared/Components/ModalButtonClose.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import CatalogItem from '@/Pages/Catalog/Components/CatalogItem.vue'
import CatalogItemForm from '@/Pages/Catalog/Components/CatalogItemForm.vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import { computed, reactive, ref, watch } from 'vue'

defineExpose({ show })
const emit = defineEmits(['refresh'])

const props = defineProps({
    title: {
        required: true,
        type: String,
    },
    route: {
        required: false,
        type: String,
        default: null,
    },
    submitBtnText: {
        required: false,
        type: String,
        default: 'Save',
    },
})

const { $t, $route } = useGlobalVariables()

const form = useForm({
    products: [],
})
const catalogItemFormRef = ref()
const products = ref([])
const filters = reactive({
    q: null,
    type: null,
})
const isFetching = ref(false)
const isShow = ref(false)
const pagination = ref({
    page: 1,
})
const endpoint = ref(props.route)

const itemTypes = computed(() => getPageProps('itemTypeOptions'))

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
    {
        text: $t('labels.actions'),
        value: 'action',
        sortable: false,
        align: 'center',
    },
]

watch(
    () => filters,
    () => {
        fetchItems()
    },
    { deep: true },
)

const fetchItems = useDebounceFn(async (page = 1) => {
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

    products.value = data

    pagination.value = {
        rowsPerPage: per_page,
        page: current_page,
        totalItems: total,
    }

    isFetching.value = false
}, 500)

function show(url = null) {
    if (url) {
        endpoint.value = url
    }

    reset()
    fetchItems()
    isShow.value = true
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    form.reset()
    form.clearErrors()
    filters.q = null
    filters.type = null
}

function submit() {
    if (!endpoint.value) {
        return console.error('Please add endpoint/route/url!')
    }

    form.transform((data) => ({
        ...data,
        products: data.products.map((product) => product.id),
    })).post(endpoint.value, {
        onSuccess: () => {
            form.reset()
            isShow.value = false
            emit('refresh')
        },
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
    >
        <CatalogItemForm
            ref="catalogItemFormRef"
            @refresh="fetchItems(1)"
        />
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ title }}
                </v-toolbar-title>
                <v-spacer />
                <ModalButtonClose @click.native="close" />
            </v-toolbar>
            <v-divider />
            <v-card-text class="dialog-form-body">
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
                                class="elevation-1 px-0 py-2"
                                @update:pagination="
                                    ({ page }) => fetchItems(page)
                                "
                            >
                                <template #items="itemProps">
                                    <CatalogItem
                                        :item="itemProps.item"
                                        :hide-actions="true"
                                        :can-delete-item="false"
                                        :can-update-item="false"
                                        :selectable="true"
                                        :is-selected="props.selected"
                                        @click:row="
                                            () =>
                                                (itemProps.selected =
                                                    !itemProps.selected)
                                        "
                                    >
                                        <template #select>
                                            <td>
                                                <v-checkbox
                                                    v-model="itemProps.selected"
                                                    primary
                                                    hide-details
                                                    @click="
                                                        () =>
                                                            (itemProps.selected =
                                                                !itemProps.selected)
                                                    "
                                                />
                                            </td>
                                        </template>
                                    </CatalogItem>
                                </template>
                                <template #no-data>
                                    <v-alert
                                        :value="true"
                                        color="info"
                                        icon="info"
                                        outline
                                        class="!tw-my-11"
                                    >
                                        <div
                                            class="tw-flex tw-items-center tw-gap-x-5"
                                        >
                                            <div>
                                                {{
                                                    $t('hints.no-results-match')
                                                }}
                                                for "{{ filters.q }}".
                                            </div>
                                            <VBtn
                                                :disabled="form.processing"
                                                color="primary"
                                                @click="
                                                    () =>
                                                        catalogItemFormRef.show(
                                                            null,
                                                            { name: filters.q },
                                                        )
                                                "
                                            >
                                                {{
                                                    $t('buttons.create-new-one')
                                                }}
                                            </VBtn>
                                        </div>
                                    </v-alert>
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
                    {{ submitBtnText ?? $t('catalog.buttons.add-to-order') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<style>
.dialog-form-body {
    height: 75vh;
}
</style>
