<script setup>
import { computed } from 'vue'
import { truncate } from '@/Composables/useUtils'
import OrderStatus from './OrderStatus.vue'

const { order } = defineProps({
    order: {
        required: true,
        type: Object,
    },
})

const orderType = computed(() => order.order_type.description.toLowerCase())
const isIncoming = computed(() => orderType.value === 'incoming')
const isOutgoing = computed(() => orderType.value === 'outgoing')

const oppositeParty = computed(() =>
    isIncoming.value ? order.client : order.contractor,
)
</script>
<template>
    <v-card
        :class="[
            'grey--text text--darken-3 order-card',
            {
                incoming: isIncoming,
                outgoing: isOutgoing,
            },
        ]"
        hover
        @click="$emit('click:card')"
    >
        <!-- For Mobile Device -->
        <v-card-title
            v-if="$vuetify.breakpoint.smAndDown"
            class="tw-bg-primary-lighter"
        >
            <div
                class="tw-flex tw-font-semibold tw-items-center tw-justify-between tw-w-full"
            >
                <div class="tw-flex">
                    <v-icon color="primary">local_shipping</v-icon>

                    <div class="ml-2">
                        {{ order.delivery_address?.short_full_address }}
                    </div>
                </div>
                <a
                    :href="order.delivery_address?.google_map?.url"
                    target="_blank"
                    @click.stop
                >
                    <v-btn
                        v-if="$vuetify.breakpoint.smAndDown"
                        color="white"
                        depressed
                        class="white--text !tw-min-w-8 my-0"
                    >
                        <v-icon
                            small
                            color="primary"
                        >
                            place
                        </v-icon>
                    </v-btn>
                </a>
            </div>
        </v-card-title>
        <v-card-text>
            <v-container
                grid-list-md
                fluid
                pa-0
            >
                <v-layout
                    row
                    :class="[
                        'subheading',
                        {
                            'tw-h-[135px]': $vuetify.breakpoint.mdAndUp,
                        },
                    ]"
                >
                    <v-flex
                        v-if="$vuetify.breakpoint.mdAndUp"
                        xs2
                        md2
                    >
                        <v-avatar
                            tile
                            class="block"
                        >
                            <v-img
                                :src="oppositeParty.photo"
                                :alt="oppositeParty.name"
                                max-height="127"
                            />
                        </v-avatar>
                    </v-flex>

                    <!-- Client Details -->
                    <v-flex
                        v-if="$vuetify.breakpoint.mdAndUp"
                        xs3
                        ml-2
                        class="primary--text"
                    >
                        <div class="tw-font-semibold">
                            {{ oppositeParty?.name }}
                        </div>
                        <div class="grey--text text--darken-3 tw-text-base">
                            {{ oppositeParty?.address?.full }}
                        </div>
                        <div class="primary--text tw-font-bold tw-text-base">
                            {{ oppositeParty.email }}
                        </div>
                        <div class="primary--text tw-font-bold tw-text-base">
                            {{ oppositeParty.phone ?? '--' }}
                        </div>
                    </v-flex>

                    <!-- Order Details -->
                    <v-flex
                        xs12
                        md5
                        class="primary--text tw-flex-col tw-items-start"
                    >
                        <div
                            v-if="$vuetify.breakpoint.mdAndUp"
                            class="tw-flex tw-font-semibold tw-items-start"
                        >
                            <v-icon color="primary">local_shipping</v-icon>

                            <div class="ml-2">
                                {{ order.delivery_address?.short_full_address }}
                            </div>
                            <a
                                target="_blank"
                                :href="order.delivery_address?.google_map?.url"
                                class="tw-ml-auto"
                                @click.stop
                            >
                                <v-icon color="primary"> place </v-icon>
                            </a>
                        </div>
                        <div
                            class="tw-text-black tw-text-base tw-whitespace-normal tw-pr-6"
                        >
                            {{
                                truncate(
                                    order.description,
                                    $vuetify.breakpoint.mdAndUp ? 348 : 167,
                                )
                            }}
                        </div>
                    </v-flex>
                    <!-- END Order Details -->
                    <v-flex
                        v-if="$vuetify.breakpoint.mdAndUp"
                        xs2
                        class="tw-flex tw-justify-end"
                    >
                        <div>
                            <OrderStatus
                                :color="order.status.color"
                                :text-color="order.status.icon_color"
                                :icon="order.status.icon"
                                :title="order.status_text"
                                :tool-tip="order.status.text"
                            />
                        </div>
                    </v-flex>
                    <!-- End of Client Details -->
                </v-layout>
            </v-container>
        </v-card-text>
        <v-divider />
        <v-card-actions class="tw-bg-primary-lighter">
            <v-layout
                v-if="$vuetify.breakpoint.smAndDown"
                row
                wrap
            >
                <v-flex xs12>
                    <div class="tw-flex tw-justify-between">
                        <div class="tw-flex tw-items-center tw-gap-x-2">
                            <v-avatar
                                tile
                                class=""
                            >
                                <v-img
                                    :src="oppositeParty.photo"
                                    :alt="oppositeParty.name"
                                    max-height="50"
                                />
                            </v-avatar>
                            <div>
                                <div class="tw-font-semibold">
                                    {{ oppositeParty?.name }}
                                </div>
                            </div>
                        </div>
                        <v-chip
                            v-tooltip="`${order?.status?.description}`"
                            label
                            :color="order?.status?.color ?? 'blue lighten-4'"
                            :text-color="order?.status?.icon_color"
                        >
                            <v-icon
                                :color="order?.status?.icon_color"
                                class="mr-1"
                            >
                                {{
                                    order?.status?.icon ?? 'playlist_add_check'
                                }}
                            </v-icon>
                        </v-chip>
                    </div>
                </v-flex>
            </v-layout>
            <v-layout
                v-if="$vuetify.breakpoint.mdAndUp"
                row
            >
                <v-flex
                    xs3
                    md2
                >
                    <!-- Order Id -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">fingerprint</v-icon>
                        <div
                            class="primary--text tw-font-medium tw-tracking-tighter"
                        >
                            {{ order.order_number }}
                        </div>
                    </div>
                </v-flex>
                <v-flex
                    xs3
                    md3
                >
                    <!-- Working Time -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">work_history</v-icon>
                        <div
                            class="primary--text tw-font-medium tw-tracking-tighter"
                        >
                            {{
                                order.total_time_rendered == 0
                                    ? '--'
                                    : order.total_time_rendered
                            }}
                        </div>
                    </div>
                </v-flex>
                <v-flex
                    xs3
                    md3
                    ml-2
                >
                    <!-- Execution Date -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">date_range</v-icon>
                        <div
                            class="primary--text tw-font-medium tw-tracking-tighter"
                        >
                            {{
                                order.planned_start_date
                                    | toDateTimeLocal('DD.MM.YYYY')
                            }}
                            -
                            {{
                                order.planned_finish_date
                                    | toDateTimeLocal('DD.MM.YYYY')
                            }}
                        </div>
                    </div>
                </v-flex>
                <v-flex
                    xs3
                    md3
                >
                    <!-- Appointment Date -->
                    <div class="tw-flex tw-gap-x-2 tw-items-center">
                        <v-icon color="primary">person_pin_circle</v-icon>
                        <div
                            class="primary--text tw-font-medium tw-tracking-tighter"
                        >
                            {{
                                order.order_date | toDateTimeLocal('DD.MM.YYYY')
                            }}
                        </div>
                    </div>
                </v-flex>
            </v-layout>
        </v-card-actions>
    </v-card>
</template>
<style scoped>
.order-card {
    &.incoming {
        border-left: 6px solid #fe4e89;
    }

    &.outgoing {
        border-left: 6px solid #4e94fe;
    }
}
</style>
