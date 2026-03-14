<script setup>
import MenuListItem from '@/Components/MenuListItem.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SingleDatePicker from '@/Pages/Order/Components/SingleDatePicker.vue'
import { watchDebounced } from '@vueuse/core'
import { reactive, ref } from 'vue'

const props = defineProps({
    pageFilters: {
        required: true,
        type: Object,
    },

    isLoading: {
        required: false,
        type: Boolean,
        default: false,
    },

    enableExporting: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits([
    'update:modelValue',
    'click:create-invoice',
    'update:filters',
    'click:bulk-export',
])
const { $t } = useGlobalVariables()

const expandFilters = ref(false)
const target = ref(null)

const propFilters = props.pageFilters
const filters = reactive({
    q: propFilters?.q,
    dateAdded: {
        from: propFilters?.dateAdded?.from,
        to: propFilters?.dateAdded?.to,
    },
    paymentDate: {
        from: propFilters?.paymentDate?.from,
        to: propFilters?.paymentDate?.to,
    },
    invoiceStatus: propFilters?.invoiceStatus,
    type: propFilters?.type,
})

const invoiceTypes = [
    {
        text: $t('order.labels.incoming-invoices'),
        value: 1,
    },
    {
        text: $t('order.labels.outgoing-invoices'),
        value: 2,
    },
]

const invoiceStatuses = [
    {
        text: $t('invoices.labels.draft'),
        value: 0,
    },
    {
        text: $t('invoices.labels.pending'),
        value: 1,
    },
    {
        text: $t('invoices.labels.partially_paid'),
        value: 3,
    },
    {
        text: $t('invoices.labels.paid'),
        value: 2,
    },
    {
        text: $t('invoices.labels.overpaid'),
        value: 4,
    },
]

watchDebounced(
    filters,
    (newFilters) => {
        emit('update:filters', newFilters)
    },
    { debounce: 500 },
)

function clearFilters() {
    filters.q = null
}

function handleInput() {
    if (filters?.q === '') {
        filters.q = null
    }
}
</script>
<template>
    <v-flex xs12>
        <v-card
            class="order-toolbar"
            color="primary-light"
        >
            <v-card-text>
                <v-layout
                    row
                    wrap
                    :class="{
                        'align-end': $vuetify.breakpoint.smAndUp,
                        'align-center': $vuetify.breakpoint.xs,
                    }"
                    align-center
                >
                    <v-flex
                        xs9
                        sm8
                        md8
                        class="tw-flex tw-items-end py-1"
                    >
                        <v-text-field
                            ref="searchField"
                            v-model="filters.q"
                            hide-details
                            :placeholder="
                                $t('invoices.hints.search-placeholder')
                            "
                            color="primary"
                            solo
                            flat
                            clearable
                            :loading="isLoading"
                            @click:clear="clearFilters"
                            @input="handleInput"
                        >
                            <template #append>
                                <v-icon
                                    color="primary"
                                    class="tw-cursor-pointer sm:tw-hidden"
                                    @click="expandFilters = !expandFilters"
                                >
                                    filter_alt
                                </v-icon>
                            </template>
                        </v-text-field>
                    </v-flex>
                    <v-flex
                        xs3
                        md4
                        class="tw-text-center tw-self-center"
                    >
                        <div
                            class="tw-flex tw-flex-col tw-align-bottom tw-align-center sm:tw-flex-row"
                        >
                            <v-btn
                                color="white"
                                large
                                depressed
                                class="white--text !tw-min-w-8 xs:tw-hidden"
                                @click="expandFilters = !expandFilters"
                            >
                                <v-icon color="primary"> filter_alt </v-icon>
                            </v-btn>
                            <v-btn
                                :disabled="!enableExporting"
                                block
                                large
                                color="primary"
                                depressed
                                class="white--text xs:!tw-my-2 xs:!tw-h-14 mx-2 xs:!tw-mx-0"
                                @click="$emit('click:bulk-export')"
                            >
                                <v-icon v-if="$vuetify.breakpoint.smAndDown">
                                    file_download
                                </v-icon>
                                <span v-if="$vuetify.breakpoint.smAndUp">
                                    {{ $t('buttons.export') }}
                                </span>
                            </v-btn>

                            <v-menu
                                offset-y
                                left
                            >
                                <template #activator="{ on }">
                                    <v-btn
                                        block
                                        large
                                        color="primary"
                                        depressed
                                        class="white--text xs:!tw-my-0 xs:!tw-h-14"
                                        v-on="on"
                                    >
                                        <v-icon
                                            v-if="$vuetify.breakpoint.smAndDown"
                                        >
                                            add
                                        </v-icon>
                                        <span>
                                            <span
                                                v-if="
                                                    $vuetify.breakpoint.mdOnly
                                                "
                                            >
                                                {{ $t('buttons.create') }}
                                            </span>
                                            <span
                                                v-if="
                                                    $vuetify.breakpoint.lgAndUp
                                                "
                                            >
                                                {{
                                                    $t(
                                                        'invoices.buttons.create-invoice',
                                                    )
                                                }}
                                            </span>
                                        </span>
                                    </v-btn>
                                </template>
                                <v-list dense>
                                    <MenuListItem
                                        icon="move_to_inbox"
                                        @click="
                                            $emit(
                                                'click:create-invoice',
                                                'incoming',
                                            )
                                        "
                                    >
                                        {{
                                            $t(
                                                'order.labels.add-incoming-invoice',
                                            )
                                        }}
                                    </MenuListItem>
                                    <MenuListItem
                                        icon="outbox"
                                        @click="
                                            $emit(
                                                'click:create-invoice',
                                                'outgoing',
                                            )
                                        "
                                    >
                                        {{
                                            $t(
                                                'order.labels.add-outgoing-invoice',
                                            )
                                        }}
                                    </MenuListItem>
                                </v-list>
                            </v-menu>
                        </div>
                    </v-flex>

                    <!-- Filters -->
                    <v-expansion-panel
                        ref="target"
                        :value="[expandFilters]"
                        expand
                        class="!tw-shadow-none mt-2"
                    >
                        <v-expansion-panel-content>
                            <v-card
                                flat
                                color="primary-light"
                            >
                                <v-card-text class="px-1">
                                    <v-layout
                                        row
                                        wrap
                                        align-center
                                    >
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <div
                                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                                >
                                                    {{
                                                        $t(
                                                            'order.labels.date_added',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :model-value.sync="
                                                            filters.dateAdded
                                                                .from
                                                        "
                                                        :is-clearable="true"
                                                        :placeholder="
                                                            $t('labels.from')
                                                        "
                                                        :is-loading="isLoading"
                                                        @update:model-value="
                                                            (value) =>
                                                                (filters.dateAdded.from =
                                                                    value)
                                                        "
                                                    />
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters.dateAdded.to
                                                        "
                                                        :placeholder="
                                                            $t('labels.to')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                            </div>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <div
                                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                                >
                                                    {{
                                                        $t(
                                                            'invoices.labels.payment-date',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters.paymentDate
                                                                .from
                                                        "
                                                        :placeholder="
                                                            $t('labels.from')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters.paymentDate
                                                                .to
                                                        "
                                                        :placeholder="
                                                            $t('labels.to')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                            </div>
                                        </v-flex>
                                        <!-- <v-flex
                                            xs12
                                            lg9
                                        >
                                            <div
                                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                                >
                                                    {{
                                                        $t(
                                                            'order.labels.inputs.planned-start-date',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters
                                                                .plannedStartDate
                                                                .from
                                                        "
                                                        :placeholder="
                                                            $t('labels.from')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters
                                                                .plannedStartDate
                                                                .to
                                                        "
                                                        :placeholder="
                                                            $t('labels.to')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                            </div>
                                        </v-flex> -->
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <div
                                                class="tw-grid md:tw-grid-cols-7 tw-grid-cols-2 tw-gap-x-2 pb-4"
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                                >
                                                    {{
                                                        $t(
                                                            'invoices.form.invoice-type',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="filters.type"
                                                        :items="invoiceTypes"
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full dense tw-bg-white"
                                                        dense
                                                        outline
                                                        flat
                                                        hide-selected
                                                        clearable
                                                        background-color="white"
                                                        color="primary"
                                                        :loading="isLoading"
                                                    />
                                                </div>
                                            </div>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <div
                                                class="tw-grid md:tw-grid-cols-7 tw-grid-cols-2 tw-gap-x-2 pb-4"
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                                >
                                                    {{
                                                        $t(
                                                            'labels.invoice-status',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="
                                                            filters.invoiceStatus
                                                        "
                                                        :items="invoiceStatuses"
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full dense tw-bg-white"
                                                        dense
                                                        outline
                                                        flat
                                                        hide-selected
                                                        clearable
                                                        background-color="white"
                                                        color="primary"
                                                        :loading="isLoading"
                                                    />
                                                </div>
                                            </div>
                                        </v-flex>
                                    </v-layout>
                                </v-card-text>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-layout>
            </v-card-text>
        </v-card>
    </v-flex>
</template>
