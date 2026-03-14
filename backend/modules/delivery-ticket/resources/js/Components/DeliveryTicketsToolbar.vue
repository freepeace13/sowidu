<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import SingleDatePicker from '@/Pages/Order/Components/SingleDatePicker.vue'
import { get } from '@vueuse/core'
import { computed, reactive, ref, watch } from 'vue'

defineProps({
    isLoading: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['click:create', 'filter:changed'])

const { $t } = useGlobalVariables()

const expandFilters = ref(false)
const target = ref(null)

const filters = reactive({
    q: null,
    deliveryDates: {
        from: null,
        to: null,
    },
    type: null,
    invoiceStatus: null,
})
const deliveryTicketTypes = computed(() =>
    getPageProps('deliveryTicketTypes', []),
)

const invoiceStatuses = [
    {
        text: $t('delivery_tickets.invoices.pending'),
        value: 0,
    },
    {
        text: $t('delivery_tickets.invoices.paid'),
        value: 1,
    },
]

watch(
    filters,
    (values) => {
        emit('filter:changed', get(values))
    },
    {
        deep: true,
    },
)

function clearFilters() {
    filters.q = null
}

function handleInput() {
    if (filters.q === '') {
        filters.q = null
    }
}
</script>
<template>
    <v-flex xs12>
        <v-card color="primary-light">
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
                        md9
                        class="tw-flex tw-items-end py-1"
                    >
                        <v-text-field
                            ref="searchField"
                            v-model="filters.q"
                            hide-details
                            :placeholder="
                                $t('delivery_tickets.form.search-order')
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
                        class="tw-text-center tw-self-center"
                    >
                        <div
                            class="tw-flex tw-flex-row tw-align-bottom tw-align-center"
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
                                block
                                large
                                color="primary"
                                depressed
                                class="white--text xs:!tw-my-0 xs:!tw-h-14"
                                @click="$emit('click:create')"
                            >
                                <v-icon v-if="$vuetify.breakpoint.smAndDown">
                                    add
                                </v-icon>

                                <span>
                                    {{ $t('buttons.create') }}
                                </span>
                            </v-btn>
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
                                                            'delivery_tickets.form.delivery_date',
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
                                                                .deliveryDates
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
                                                                .deliveryDates
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
                                                            'delivery_tickets.form.ticket-type',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="filters.type"
                                                        :items="
                                                            deliveryTicketTypes
                                                        "
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full dense tw-bg-white"
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
