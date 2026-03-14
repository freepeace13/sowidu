<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import SingleDatePicker from './SingleDatePicker.vue'
import { getPageProps } from '@/Composables/useUtils'
import { watchDebounced } from '@vueuse/core'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { nextTick } from 'vue'

const props = defineProps({
    modelValue: {
        required: true,
        type: [Object, Array],
    },
    isShow: {
        required: false,
        type: Boolean,
        default: false,
    },
    isLoading: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const { $root } = useGlobalVariables()

console.log(props)
const target = ref(null)
const emittedFilterClear = ref(false)
const showFilters = ref([])
const filters = reactive({
    status: props?.modelValue?.value?.status,
    invoice: props?.modelValue?.value?.invoice ?? 'all',
    q: props?.modelValue?.value?.q,
    dateAdded: {
        from: null,
        to: null,
    },
    started: {
        from: null,
        to: null,
    },
    plannedFinished: {
        from: null,
        to: null,
    },
})

const orderStatuses = computed(() => getPageProps('orderStatuses'))

onMounted(() => {
    $root.$on('orders.filters.clear', resetFilters)
})

onBeforeUnmount(() => {
    $root.$off('orders.filters.clear', resetFilters)
})

watch(
    () => props.isShow,
    (newValue) => {
        showFilters.value = []
        if (newValue) {
            showFilters.value = [{ 0: true }]
        }
    },
)

watchDebounced(
    filters,
    (newFilters) => {
        if (emittedFilterClear.value) {
            emittedFilterClear.value = false
            return
        }

        emit('update:modelValue', newFilters)
    },
    { debounce: 500, maxWait: 1000 },
)

function resetFilters() {
    emittedFilterClear.value = true

    // Add nextTick to ensure the filters are reset after the event is emitted
    nextTick(() => {
        filters.status = null
        filters.q = null
        filters.dateAdded.from = null
        filters.dateAdded.to = null
        filters.started.from = null
        filters.started.to = null
        filters.plannedFinished.from = null
        filters.plannedFinished.to = null
        filters.invoice = 'all'
    })
}
</script>
<template>
    <v-expansion-panel
        ref="target"
        v-model="showFilters"
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
                                    {{ $t('order.labels.date_added') }}
                                </div>
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="
                                            filters.dateAdded.from
                                        "
                                        :placeholder="$t('order.labels.from')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="filters.dateAdded.to"
                                        :placeholder="$t('order.labels.to')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                            </div>
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
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="filters.started.from"
                                        :placeholder="$t('order.labels.from')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="filters.started.to"
                                        :placeholder="$t('order.labels.to')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                            </div>
                            <div
                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                            >
                                <div
                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                >
                                    {{
                                        $t(
                                            'order.labels.inputs.planned-finish-date',
                                        )
                                    }}
                                </div>
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="
                                            filters.plannedFinished.from
                                        "
                                        :placeholder="$t('order.labels.from')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                                <div class="tw-col-span-1 md:tw-col-span-3">
                                    <SingleDatePicker
                                        :model-value.sync="
                                            filters.plannedFinished.to
                                        "
                                        :placeholder="$t('order.labels.to')"
                                        :is-loading="isLoading"
                                    />
                                </div>
                            </div>
                            <div
                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                            >
                                <div
                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                >
                                    {{ $t('order.labels.order_status') }}
                                </div>
                                <div class="tw-col-span-6">
                                    <v-select
                                        v-model="filters.status"
                                        :items="orderStatuses"
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
                                        :placeholder="
                                            $t('order.labels.filter-status')
                                        "
                                        :loading="isLoading"
                                    />
                                </div>
                            </div>
                            <div
                                class="tw-grid tw-grid-cols-2 md:tw-grid-cols-7 tw-gap-x-2 pb-4"
                            >
                                <div
                                    class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
                                >
                                    {{ $t('order.labels.filter-invoice') }}
                                </div>
                                <div class="tw-col-span-6">
                                    <v-radio-group
                                        v-model="filters.invoice"
                                        row
                                    >
                                        <v-radio
                                            value="all"
                                            label="All"
                                        />
                                        <v-radio
                                            value="with_invoice"
                                            label="With Invoice"
                                        />
                                        <v-radio
                                            value="no_invoice"
                                            label="No Invoice"
                                        />
                                    </v-radio-group>
                                </div>
                            </div>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>
./SingleDatePicker.vue
