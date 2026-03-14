<script setup>
import ModalButtonClose from '@/Apps/Shared/Components/ModalButtonClose.vue'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import CatalogItem from '@/Pages/Catalog/Components/CatalogItem.vue'
import CatalogItemForm from '@/Pages/Catalog/Components/CatalogItemForm.vue'
import { useForm, usePage } from '@inertiajs/vue2'
import { useDebounceFn, watchWithFilter } from '@vueuse/core'
import axios from 'axios'
import { reactive, ref } from 'vue'

defineExpose({
    show,
})

const props = defineProps({
    offer: {
        type: Object,
    },
})

defineEmits(['close', 'click:attach'])

const { $route } = useGlobalVariables()

const isShow = ref(false)
const items = ref([])
const catalogItemFormRef = ref(null)
const form = useForm({
    items: [],
})

const filters = reactive({
    q: null,
    type: null,
})

watchWithFilter(filters, () => {
    fetchItems()
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

function show() {
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

const fetchItems = useDebounceFn(async () => {
    isFetching.value = true
    const { page } = pagination.value
    if (page === 1) {
        items.value = []
    }

    const params = { ...filters, page }

    const {
        data: { data, per_page, current_page, total },
    } = await axios.get($route('json.catalog.items.index', params))

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
    const offer = props.offer

    form.transform((data) => ({
        ...data,
        items: data.items.map(({ id }) => id),
    })).post($route('offers.items.store', { offer }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => close(),
        only: ['items', 'offer'],
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        fullscreen
    >
        <CatalogItemForm
            ref="catalogItemFormRef"
            @refresh="fetchItems"
        />
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('offer.labels.attach_items') }}
                </v-toolbar-title>
                <v-spacer />
                <ModalButtonClose @click.native="close" />
            </v-toolbar>
            <v-divider />
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
                                clearable
                                :items="itemTypes"
                                :loading="isFetching"
                                :disabled="isFetching"
                                :hide-details="true"
                                :label="$t('labels.inputs.filter-by-type')"
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-text-right"
                        >
                            <v-btn
                                :disabled="form.processing"
                                color="info"
                                depressed
                                @click="() => catalogItemFormRef.show()"
                            >
                                {{ $t('catalog.buttons.add-new-item') }}
                            </v-btn>
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-data-table
                                v-model="form.items"
                                :headers="[
                                    {
                                        text: $t('catalog.labels.item.image'),
                                        value: 'image',
                                        sortable: false,
                                    },
                                    {
                                        text: $t(
                                            'catalog.labels.item.item-name',
                                        ),
                                        value: 'name',
                                        sortable: false,
                                    },
                                    {
                                        text: $t(
                                            'catalog.labels.item.description',
                                        ),
                                        value: 'description',
                                        sortable: false,
                                    },
                                    {
                                        text: $t('catalog.labels.item.type'),
                                        value: 'type',
                                        sortable: false,
                                    },
                                    {
                                        text: $t(
                                            'catalog.labels.item.internal-id',
                                        ),
                                        value: 'internal_id',
                                        sortable: false,
                                    },
                                    {
                                        text: $t(
                                            'catalog.labels.item.vendor-id',
                                        ),
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
                                @update:pagination="
                                    (payload) => paginate(payload)
                                "
                            >
                                <template #items="itemProps">
                                    <CatalogItem
                                        :item="itemProps.item"
                                        :hide-actions="true"
                                        :can-delete-item="false"
                                        :can-update-item="false"
                                        :selectable="true"
                                        :is-selected="itemProps.selected"
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
                                                        itemProps.selected =
                                                            !itemProps.selected
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
                                        class="my-3"
                                    >
                                        {{
                                            $t('hints.no-results-matching', {
                                                keyword: filters.q,
                                            })
                                        }}
                                        <v-btn
                                            small
                                            color="info"
                                            @click="
                                                () => {
                                                    catalogItemFormRef.show(
                                                        null,
                                                        {
                                                            name: filters.q,
                                                        },
                                                    )
                                                }
                                            "
                                        >
                                            {{
                                                $t(
                                                    'catalog.buttons.add-new-item',
                                                )
                                            }}
                                        </v-btn>
                                    </v-alert>
                                </template>
                            </v-data-table>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="
                        () => {
                            close()
                        }
                    "
                >
                    {{ $t('buttons.close') }}
                </v-btn>
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing || !form.items.length"
                    @click="submit"
                >
                    {{ $t('offer.buttons.attach_to_offer') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
