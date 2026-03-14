<script>
import CatalogItemForm from './Components/CatalogItemForm.vue'
import CatalogItem from './Components/CatalogItem.vue'
import { useDebounceFn } from '@vueuse/core'
import { authCan } from '@/Composables/useAuth'
import CatalogItemDetailsModal from './Components/CatalogItemDetailsModal.vue'

export default {
    components: { CatalogItem, CatalogItemForm, CatalogItemDetailsModal },

    props: {
        items: {
            type: Object,
            required: true,
        },
        itemTypeOptions: {
            type: Array,
            required: true,
        },
        filters: {
            type: [Object, Array],
            required: true,
        },
        currency: {
            type: Object,
            required: false,
            default: () => ({
                name: null,
                symbol: null,
            }),
        },
    },

    data: (vm) => ({
        pagination: {},
        isLoading: false,
        pageFilters: {
            q: vm.filters?.q,
            type: vm.filters?.type,
        },
    }),

    computed: {
        headers() {
            return [
                {
                    text: this.$t('catalog.labels.item.image'),
                    value: 'image',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.item-name'),
                    value: 'name',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.description'),
                    value: 'description',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.type'),
                    value: 'type',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.internal-id'),
                    value: 'internal_id',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.vendor-id'),
                    value: 'vendor_id',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.manufacture-id'),
                    value: 'manufacture_id',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.unit'),
                    value: 'unit',
                    sortable: false,
                },
                {
                    text: this.$t('catalog.labels.item.purchasing-price'),
                    value: 'purchasing_price',
                    sortable: false,
                    align: 'center',
                },
                {
                    text: this.$t('catalog.labels.item.selling-price'),
                    value: 'selling_price',
                    align: 'center',
                    sortable: false,
                },
                {
                    text: this.$t('labels.actions'),
                    value: 'action',
                    sortable: false,
                    align: 'center',
                },
            ]
        },

        hasFilters() {
            return (
                Object.values(this.filters)
                    .filter((filter) => !!filter)
                    .flat().length > 0
            )
        },

        authCanDeleteItem() {
            return authCan('can delete catalog items')
        },

        authCanUpdateItem() {
            return authCan('can create catalog items')
        },
    },

    watch: {
        pagination(newValue) {
            if (this.items.current_page == newValue.page) {
                return
            }

            // Paginate
            this.fetch(newValue.page)
        },

        pageFilters: {
            handler() {
                this.filtersDebounce()
            },
            deep: true,
        },
    },

    created() {
        this.filtersDebounce = useDebounceFn(async () => {
            this.fetch(1)
        }, 500)
    },

    methods: {
        fetch(page = 1) {
            this.$inertia.reload({
                preserveScroll: true,
                preserveState: true,
                data: { page, ...this.pageFilters },
                only: ['items', 'filters'],
                onBefore: () => (this.isLoading = true),
                onFinish: () => (this.isLoading = false),
            })
        },

        clearAllFilters() {
            this.pageFilters = {
                q: null,
                type: null,
            }
        },

        deleting(item) {
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this item?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('catalogs.items.destroy', { item }),
                        {
                            preserveScroll: true,
                            only: ['errors', 'flash', 'items'],
                        },
                    )
                },
            })
        },
    },
}
</script>
<template>
    <div class="todo">
        <v-toolbar
            color="transparent"
            flat
        >
            <v-toolbar-title class="title sm:tw-w-auto tw-w-full">
                <v-icon
                    color="grey darken-1"
                    class="mr-1"
                    :medium="$vuetify.breakpoint.mdAndUp"
                    left
                >
                    menu_book
                </v-icon>
                <span class="md:tw-text-xl tw-text-lg">
                    {{ $t('catalog.labels.catalogs') }}
                </span>
            </v-toolbar-title>
            <v-spacer />
            <v-layout
                row
                wrap
            >
                <v-flex>
                    <v-text-field
                        v-model="pageFilters.q"
                        :placeholder="`${$t('labels.search')}...`"
                        solo
                        hide-details
                        outline
                        single-line
                        class="small white"
                    >
                        <template #prepend-inner>
                            <v-icon>search</v-icon>
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
            <v-spacer class="hidden-sm-and-down" />
            <v-btn
                color="primary"
                class="tw-min-w-[32px]"
                :small="$vuetify.breakpoint.xsOnly"
                @click="$refs.catalogItemForm.show()"
            >
                <v-icon :left="$vuetify.breakpoint.smAndUp">add</v-icon>
                {{
                    $vuetify.breakpoint.xs
                        ? ''
                        : $t('catalog.buttons.add-new-item')
                }}
            </v-btn>
        </v-toolbar>
        <v-divider />
        <v-layout
            pa-4
            align-center
        >
            <v-flex xs3>
                <v-select
                    v-model="pageFilters.type"
                    :items="itemTypeOptions"
                    :label="$t('catalog.labels.filter-by-type')"
                    outline
                    no-filter
                    full-width
                    :loading="isLoading"
                    :disabled="isLoading"
                    hide-details
                    solo
                    class="small white"
                />
            </v-flex>
            <v-flex xs3>
                <v-btn
                    v-show="hasFilters"
                    outlined
                    depressed
                    color="primary"
                    :loading="isLoading"
                    :disabled="isLoading"
                    @click="clearAllFilters"
                >
                    <v-icon small>clear</v-icon>
                    <span class="tw-text-xs">
                        {{ $t('buttons.clear-filters') }}
                    </span>
                </v-btn>
            </v-flex>
        </v-layout>
        <v-data-table
            :headers="headers"
            :items="items.data"
            :loading="isLoading"
            :pagination.sync="pagination"
            :total-items="items.total"
            :rows-per-page-items="[items.per_page]"
            :no-data-text="$t('catalog.labels.no-items')"
            class="elevation-1 px-4 py-2"
        >
            <template #items="{ item }">
                <CatalogItem
                    :item="item"
                    :currency="currency"
                    :can-delete-item="authCanDeleteItem"
                    :can-update-item="authCanUpdateItem"
                    @click:delete="(item) => deleting(item)"
                    @click:edit="(item) => $refs.catalogItemForm.show(item)"
                    @click:show-details="
                        (item) => $refs.catalogItemDetailsModal.show(item)
                    "
                />
            </template>
        </v-data-table>
        <CatalogItemForm ref="catalogItemForm" />
        <CatalogItemDetailsModal ref="catalogItemDetailsModal" />
    </div>
</template>
