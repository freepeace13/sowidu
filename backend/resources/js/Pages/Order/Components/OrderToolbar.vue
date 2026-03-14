<template>
    <v-toolbar
        absolute
        top
        flat
        color="white"
    >
        <v-toolbar-title v-if="$vuetify.breakpoint.smAndUp">
            {{ $t('order.labels.orderbook') }}
        </v-toolbar-title>
        <v-spacer />
        <v-toolbar-items
            :class="[
                'tw-items-center tw-gap-x-2',
                {
                    'w-full': $vuetify.breakpoint.xs,
                },
            ]"
        >
            <div class="">
                <v-text-field
                    v-model="filters.q"
                    :placeholder="`${$t('labels.search')}...`"
                    single-line
                    flat
                    outline
                    prepend-inner-icon="search"
                    :loading="isLoading"
                    :class="[
                        'dense tw-items-center tw-w-full',
                        {
                            small: $vuetify.breakpoint.smAndDown,
                        },
                    ]"
                    :value="search"
                    hide-details
                />
            </div>

            <div class="tw-max-w-[252px]">
                <v-select
                    v-model="filters.status"
                    :items="orderStatuses"
                    :menu-props="{
                        nudgeBottom: 38,
                        left: true,
                    }"
                    solo
                    hide-details
                    full-width
                    class="tw-w-full dense"
                    dense
                    outline
                    flat
                    hide-selected
                    clearable
                    label="Filter by Status"
                />
            </div>
            <div class="tw-flex">
                <date-range-picker
                    ref="picker"
                    v-model="dateRanges"
                    class="tw-w-full"
                    :show-dropdowns="true"
                    :single-datepicker="true"
                    :locale-data="{
                        firstDay: 1,
                        format: 'mmm dd, yyyy',
                    }"
                    opens="left"
                    @update="filterByDateRange"
                >
                    <template #input="picker">
                        <v-text-field
                            :loading="isLoading"
                            :disabled="isLoading"
                            color="primary"
                            class="dense"
                            solo
                            label="Filter by Dates"
                            outline
                            :hide-details="true"
                            :value="getPickerDates(picker)"
                        />
                    </template>
                </date-range-picker>
            </div>
            <div
                v-show="hasFilters"
                class="tw-flex tw-h-[39px]"
            >
                <v-btn
                    small
                    color="primary"
                    @click="clearAllFilters"
                >
                    Clear
                </v-btn>
            </div>
        </v-toolbar-items>
    </v-toolbar>
</template>
<script>
import { useDateFormat } from '@/Composables/useDayJs'
import DateRangePicker from 'vue2-daterange-picker'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    components: { DateRangePicker },
    props: {
        isLoading: {
            required: false,
            type: Boolean,
            default: false,
        },
        initialSearch: {
            required: false,
            type: String,
            default: null,
        },
        orderStatuses: {
            required: false,
            type: Array,
            default: () => [],
        },
    },

    data: () => ({
        search: null,
        filters: {
            status: null,
            q: null,
            dates: null,
        },
        dateRanges: {
            startDate: null,
            endDate: null,
        },
    }),

    computed: {
        hasFilters() {
            return Object.values(this.filters)
                .filter((filter) => !!filter)
                .flat().length
        },
    },

    watch: {
        filters: {
            handler: function (filters) {
                this.$emit('filter', filters)
            },
            deep: true,
        },
    },

    mounted() {
        if (this.initialSearch) this.search = this.initialSearch

        this.$inertia.reload({ only: ['orderStatuses'] })
    },

    methods: {
        getPickerDates({ startDate, endDate }) {
            if (!startDate && !endDate) return ''

            const start = useDateFormat(startDate, 'MMM DD, YYYY')
            const end = useDateFormat(endDate, 'MMM DD, YYYY')

            return `${start} - ${end}`
        },

        filterByDateRange({ startDate, endDate }) {
            this.filters.dates = {
                start: useDateFormat(startDate, 'YYYY-MM-DD'),
                end: useDateFormat(endDate, 'YYYY-MM-DD'),
            }
        },

        clearAllFilters() {
            this.filters = {
                q: null,
                dates: null,
                status: null,
            }

            this.dateRanges = {
                startDate: null,
                endDate: null,
            }
        },
    },
}
</script>
<style lang="scss">
.vue-daterange-picker {
    .form-control {
        border: none;
        @apply tw-px-0;
    }
}
</style>
