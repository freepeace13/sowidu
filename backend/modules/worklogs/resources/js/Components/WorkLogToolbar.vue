<script setup>
import { authCan } from '@/Composables/useAuth'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import SingleDatePicker from '@/Pages/Order/Components/SingleDatePicker.vue'
import { watchDebounced } from '@vueuse/core'
import { computed, reactive, ref, toRef } from 'vue'
import FilterField from './FilterField.vue'

const props = defineProps({
    modelValue: {
        required: true,
        type: Object,
    },

    isLoading: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'click:create-work-log'])
const { $t } = useGlobalVariables()

const expandFilters = ref(false)
const target = ref(null)

const modelValue = toRef(props, 'modelValue')
const filters = reactive({
    q: modelValue.value.q,
    events: modelValue.value?.events ?? [],
    employees: modelValue.value?.employees ?? [],
    dates: {
        from: modelValue.value?.dates?.from,
        to: modelValue.value?.dates?.to,
    },
    order: modelValue.value?.order,
    address: modelValue.value?.address,
    invoiceStatus: modelValue.value?.invoiceStatus,
})

const eventItems = computed(() => getPageProps('filterByEvents'))
const employeesItems = computed(() => getPageProps('employees'))
const invoiceStatusItems = [
    { id: 'paid', name: $t('labels.paid') },
    { id: 'unpaid', name: $t('labels.unpaid') },
    { id: 'open', name: $t('work_log.labels.open') },
]
watchDebounced(
    filters,
    (newFilters) => {
        emit('update:modelValue', newFilters)
    },
    { debounce: 500 },
)

function clearFilters() {
    filters.value.q = null
}
</script>
<template>
    <v-flex xs12>
        <v-card
            class="work-log-toolbar"
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
                        md9
                        class="tw-flex tw-items-end py-1"
                    >
                        <v-text-field
                            ref="searchField"
                            v-model="filters.q"
                            hide-details
                            :placeholder="
                                $t('work_log.hints.search-placeholder')
                            "
                            color="primary"
                            solo
                            flat
                            clearable
                            :loading="isLoading"
                            @click:clear="clearFilters"
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
                                v-if="authCan('can add manual work log entry')"
                                color="primary"
                                block
                                large
                                depressed
                                class="white--text xs:!tw-my-0 xs:!tw-h-14"
                                @click="$emit('click:create-work-log')"
                            >
                                <span v-show="$vuetify.breakpoint.mdAndDown">
                                    <v-icon
                                        class="tw-mr-2"
                                        color="white"
                                    >
                                        add
                                    </v-icon>
                                </span>
                                <span
                                    v-show="$vuetify.breakpoint.mdAndDown"
                                    class="tw-mr-2"
                                    v-text="$t('buttons.create')"
                                />
                                <span
                                    v-show="$vuetify.breakpoint.lgAndUp"
                                    class="tw-mr-2"
                                    v-text="
                                        $t(
                                            'work_log.labels.create-manual-entry',
                                        )
                                    "
                                />
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
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-event',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-6"
                                                >
                                                    <v-select
                                                        v-model="filters.events"
                                                        :items="eventItems"
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full tw-bg-white"
                                                        outline
                                                        flat
                                                        hide-selected
                                                        clearable
                                                        background-color="white"
                                                        color="primary"
                                                        :loading="isLoading"
                                                        multiple
                                                        :menu-props="{
                                                            bottom: true,
                                                            offsetY: true,
                                                        }"
                                                        deletable-chips
                                                        chips
                                                    >
                                                        <template
                                                            #item="{ item }"
                                                        >
                                                            <v-list-tile-content>
                                                                <v-chip
                                                                    label
                                                                    :color="
                                                                        item.color
                                                                    "
                                                                    text-color="white"
                                                                >
                                                                    <span
                                                                        class="tw-text-xs"
                                                                    >
                                                                        {{
                                                                            item.text
                                                                        }}
                                                                    </span>
                                                                </v-chip>
                                                            </v-list-tile-content>
                                                        </template>
                                                        <template
                                                            #selection="{
                                                                item,
                                                                index,
                                                                selected,
                                                            }"
                                                        >
                                                            <v-chip
                                                                :selected="
                                                                    selected
                                                                "
                                                                :color="
                                                                    item.color
                                                                "
                                                                text-color="white"
                                                                close
                                                                label
                                                                @input="
                                                                    filters.events.splice(
                                                                        index,
                                                                        1,
                                                                    )
                                                                "
                                                            >
                                                                <span>{{
                                                                    item.text
                                                                }}</span>
                                                            </v-chip>
                                                        </template>
                                                    </v-select>
                                                </div>
                                            </FilterField>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-employees',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-6"
                                                >
                                                    <v-select
                                                        v-model="
                                                            filters.employees
                                                        "
                                                        :items="employeesItems"
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full tw-bg-white"
                                                        outline
                                                        flat
                                                        hide-selected
                                                        clearable
                                                        background-color="white"
                                                        color="primary"
                                                        :loading="isLoading"
                                                        :menu-props="{
                                                            bottom: true,
                                                            offsetY: true,
                                                        }"
                                                        deletable-chips
                                                        chips
                                                        multiple
                                                        item-value="id"
                                                    >
                                                        <template
                                                            #item="{ item }"
                                                        >
                                                            <v-list-tile-content>
                                                                <v-chip label>
                                                                    <v-avatar
                                                                        class="mr-3"
                                                                    >
                                                                        <img
                                                                            :src="
                                                                                item?.photo
                                                                            "
                                                                            :alt="
                                                                                item?.name
                                                                            "
                                                                            width="35"
                                                                        />
                                                                    </v-avatar>
                                                                    <span
                                                                        class=""
                                                                    >
                                                                        {{
                                                                            item?.alias_name ??
                                                                            item?.name
                                                                        }}
                                                                    </span>
                                                                </v-chip>
                                                            </v-list-tile-content>
                                                        </template>
                                                        <template
                                                            #selection="{
                                                                item,
                                                                index,
                                                                selected,
                                                            }"
                                                        >
                                                            <v-chip
                                                                label
                                                                :selected="
                                                                    selected
                                                                "
                                                                close
                                                                @input="
                                                                    filters.employees.splice(
                                                                        index,
                                                                        1,
                                                                    )
                                                                "
                                                            >
                                                                <v-avatar
                                                                    class="mr-3"
                                                                >
                                                                    <img
                                                                        :src="
                                                                            item?.photo
                                                                        "
                                                                        :alt="
                                                                            item?.name
                                                                        "
                                                                        width="35"
                                                                    />
                                                                </v-avatar>
                                                                <span>{{
                                                                    item?.alias_name ??
                                                                    item?.name
                                                                }}</span>
                                                            </v-chip>
                                                        </template>
                                                    </v-select>
                                                </div>
                                            </FilterField>
                                        </v-flex>

                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-address',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-6"
                                                >
                                                    <v-text-field
                                                        v-model="
                                                            filters.address
                                                        "
                                                        hide-details
                                                        :placeholder="
                                                            $t(
                                                                'work_log.labels.filter-by-address',
                                                            )
                                                        "
                                                        color="primary"
                                                        solo
                                                        flat
                                                        clearable
                                                        :loading="isLoading"
                                                        @click:clear="
                                                            clearFilters
                                                        "
                                                    />
                                                </div>
                                            </FilterField>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-date-range',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :is-clearable="true"
                                                        :model-value.sync="
                                                            filters.dates.from
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
                                                            filters.dates.to
                                                        "
                                                        :placeholder="
                                                            $t('labels.to')
                                                        "
                                                        :is-loading="isLoading"
                                                    />
                                                </div>
                                            </FilterField>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-order',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-text-field
                                                        v-model="filters.order"
                                                        hide-details
                                                        :placeholder="
                                                            $t(
                                                                'work_log.labels.filter-by-order',
                                                            )
                                                        "
                                                        color="primary"
                                                        solo
                                                        flat
                                                        clearable
                                                        :loading="isLoading"
                                                        @click:clear="
                                                            clearFilters
                                                        "
                                                    />
                                                </div>
                                            </FilterField>
                                        </v-flex>
                                        <v-flex
                                            xs12
                                            lg9
                                        >
                                            <FilterField
                                                :label="
                                                    $t(
                                                        'work_log.labels.filter-by-invoice-status',
                                                    )
                                                "
                                            >
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="
                                                            filters.invoiceStatus
                                                        "
                                                        :items="
                                                            invoiceStatusItems
                                                        "
                                                        solo
                                                        hide-details
                                                        full-width
                                                        class="tw-w-full tw-bg-white"
                                                        outline
                                                        flat
                                                        hide-selected
                                                        clearable
                                                        background-color="white"
                                                        color="primary"
                                                        :loading="isLoading"
                                                        :menu-props="{
                                                            bottom: true,
                                                            offsetY: true,
                                                        }"
                                                        item-text="name"
                                                        item-value="id"
                                                    />
                                                </div>
                                            </FilterField>
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
