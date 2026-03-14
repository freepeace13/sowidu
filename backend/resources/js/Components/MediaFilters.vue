<template>
    <v-menu
        :close-on-content-click="false"
        content-class="hide-overflow sm:tw-max-w-4/5 tw-max-w-full"
        offset-overflow
        offset-y
        v-bind="$attrs"
    >
        <template #activator="{ on }">
            <v-btn
                :depressed="hasNoFilterUsed"
                :outline="!hasNoFilterUsed"
                class="tw-min-w-[32px]"
                :small="smallButton"
                v-on="on"
            >
                <v-icon :left="$vuetify.breakpoint.smAndUp">filter_alt</v-icon>
                {{ $vuetify.breakpoint.xs ? '' : $t('labels.filters') }}
                <v-icon
                    v-if="$vuetify.breakpoint.smAndUp"
                    right
                >
                    arrow_drop_down
                </v-icon>
            </v-btn>
        </template>

        <v-card
            width="600"
            class="tw-max-w-full"
            flat
        >
            <v-card-title class="tw-font-medium md:tw-text-xl tw-text-lg">
                <v-icon
                    left
                    class="hidden-xs-only"
                >
                    filter_alt
                </v-icon>
                {{ $t('labels.filters') }}

                <v-spacer />

                <v-btn
                    class="ma-0 mr-2"
                    :small="$vuetify.breakpoint.xsOnly"
                    medium
                    depressed
                    @click="resetFilters"
                >
                    {{ $t('buttons.reset-filters') }}
                </v-btn>
            </v-card-title>

            <v-toolbar
                color="transparent"
                flat
            >
                <v-layout align-center>
                    <v-flex>
                        <v-btn-toggle
                            v-model="filter"
                            style="width: 100%"
                            mandatory
                            @change="filterCategoryChanged"
                        >
                            <v-btn
                                v-for="(
                                    categoryLabel, categoryKey
                                ) in filterCategories"
                                :key="categoryKey"
                                :value="categoryKey"
                                :small="$vuetify.breakpoint.xsOnly"
                                color="primary"
                                flat
                                outline
                                style="flex: 1"
                            >
                                {{ categoryLabel }}
                            </v-btn>
                        </v-btn-toggle>
                    </v-flex>
                </v-layout>
            </v-toolbar>
            <v-card-text v-if="filter == 'address'">
                <v-text-field
                    v-model="address"
                    single-line
                    hide-details
                    outline
                    :placeholder="$t('hints.type-address-here')"
                    @input="addressChanged"
                />
                <div class="tw-flex tw-justify-end">
                    <v-checkbox
                        v-model="noCategory"
                        single-line
                        hide-details
                        height="30"
                        color="primary"
                        class="tw-mt-0 tw-mb-0 tw-px-2 small tw-flex-none"
                    >
                        <template #label>
                            <div class="tw-capitalize tw-italic tw-text-sm">
                                {{ $t('media.labels.no-category') }}
                            </div>
                        </template>
                    </v-checkbox>
                    <v-checkbox
                        v-model="noAddress"
                        single-line
                        hide-details
                        height="30"
                        color="primary"
                        class="tw-mt-0 tw-mb-0 tw-px-2 small tw-flex-none"
                    >
                        <template #label>
                            <div class="tw-capitalize tw-italic tw-text-sm">
                                {{ $t('media.labels.no-address') }}
                            </div>
                        </template>
                    </v-checkbox>
                </div>
            </v-card-text>
            <FilterByType
                v-show="filter == 'type'"
                v-model="type"
                @input="filterChanged()"
            />
            <FilterByCategory
                v-show="filter == 'category'"
                v-model="category"
                :categories="mediaCategories"
                @input="filterChanged()"
            />
        </v-card>
    </v-menu>
</template>

<script>
import FilterByCategory from '@/Pages/Media/Partials/FilterByCategory.vue'
import FilterByType from '@/Pages/Media/Partials/FilterByType.vue'
import { useDebounceFn } from '@vueuse/shared'

export default {
    components: { FilterByType, FilterByCategory },
    props: {
        mediaCategories: {
            type: Array,
            required: true,
        },

        smallButton: {
            required: false,
            type: Boolean,
            default: false,
        },
    },
    data: () => ({
        filter: 'any',
        type: [],
        category: [],
        address: null,
        isLoading: false,
        noAddress: false,
        noCategory: false,
    }),
    computed: {
        filterCategories() {
            return {
                any: this.$t('media.categories.any'),
                address: this.$t('media.categories.address'),
                type: this.$t('media.categories.type'),
                category: this.$t('media.categories.category'),
            }
        },
        hasNoFilterUsed() {
            return (
                !this.address &&
                !this.category.length &&
                !this.type.length &&
                !this.noAddress
            )
        },
    },

    watch: {
        noAddress() {
            this.filterChanged()
        },
        noCategory() {
            this.filterChanged()
        },
    },
    created() {
        this.addressChanged = useDebounceFn(() => {
            this.filterChanged()
        }, 400)
    },
    methods: {
        filterChanged() {
            const address = this.address
            const type = this.type
            const category = this.category
            const noAddress = this.noAddress
            const noCategory = this.noCategory

            this.$emit('filter', {
                address,
                type,
                category,
                noAddress,
                noCategory,
            })
        },
        resetFilters() {
            this.filter = 'any'
            this.address = null
            this.type = []
            this.category = []
            this.noAddress = false
            this.noCategory = false
            this.$emit('filter', {})
        },
        filterCategoryChanged(filter) {
            if (filter == 'any') this.resetFilters()
        },
        addressSelected(address) {
            this.address = address
            if (!Object.hasOwn(address, 'full')) return
            this.$emit('filter', {
                address: address.full,
            })
        },
    },
}
</script>
