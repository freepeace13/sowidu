<script setup>
import MenuListItem from '@/Components/MenuListItem.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import SingleDatePicker from '@/Pages/Order/Components/SingleDatePicker.vue'
import { reactive, ref, watch } from 'vue'

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
})

const emit = defineEmits([
    'click:create-incoming-offer',
    'click:create-outgoing-offer',
    'update:filters',
])
const { $t } = useGlobalVariables()

const expandFilters = ref(false)
const target = ref(null)

const propFilters = props.pageFilters
const filters = reactive({
    q: propFilters?.q,
    offerDate: {
        from: propFilters?.offerDate?.from,
        to: propFilters?.offerDate?.to,
    },
    status: propFilters?.status,
    type: propFilters?.type,
})

const offerTypes = getPageProps('offerTypes')
const offerStatuses = getPageProps('offerStatuses')

watch(
    filters,
    (newFilters) => {
        emit('update:filters', newFilters)
    },
    { deep: true },
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
                            :placeholder="$t('offer.hints.search-offer')"
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
                            class="tw-flex tw-flex-col tw-align-bottom tw-align-center xs:tw-block sm:tw-flex-row"
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
                                        class="white--text xs:!tw-my-0 xs"
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
                                                {{ $t('offer.buttons.create') }}
                                            </span>
                                        </span>
                                    </v-btn>
                                </template>
                                <v-list dense>
                                    <MenuListItem
                                        icon="call_received"
                                        @click="
                                            $emit('click:create-incoming-offer')
                                        "
                                    >
                                        {{ $t('offer.labels.incoming_offer') }}
                                    </MenuListItem>
                                    <MenuListItem
                                        icon="call_made"
                                        @click="
                                            $emit('click:create-outgoing-offer')
                                        "
                                    >
                                        {{ $t('offer.labels.outgoing_offer') }}
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
                                                            'offer.inputs.offer_date',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-1 md:tw-col-span-3"
                                                >
                                                    <SingleDatePicker
                                                        :model-value.sync="
                                                            filters.offerDate
                                                                .from
                                                        "
                                                        :is-clearable="true"
                                                        :placeholder="
                                                            $t('labels.from')
                                                        "
                                                        :is-loading="isLoading"
                                                        @update:model-value="
                                                            (value) =>
                                                                (filters.offerDate.from =
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
                                                            filters.offerDate.to
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
                                                        $t('offer.inputs.type')
                                                    }}
                                                </div>
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="filters.type"
                                                        :items="offerTypes"
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
                                                    {{ $t('labels.status') }}
                                                </div>
                                                <div
                                                    class="tw-col-span-2 md:tw-col-span-3"
                                                >
                                                    <v-select
                                                        v-model="filters.status"
                                                        :items="offerStatuses"
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
