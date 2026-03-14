<template>
    <div class="w-full">
        <v-toolbar
            flat
            elevation-5
            color="white"
            class="order-details-toolbar tw-z-[1000]"
        >
            <v-toolbar-title v-if="$vuetify.breakpoint.mdAndUp">
                {{ $t('order.labels.order-details') }}
            </v-toolbar-title>

            <v-spacer v-if="$vuetify.breakpoint.mdAndUp" />

            <v-btn
                v-if="viewer?.can_view_reports"
                :block="$vuetify.breakpoint.smAndDown"
                color="purple darken-2 white--text"
                @click="$refs.orderReportsRef.show()"
            >
                <v-icon v-if="$vuetify.breakpoint.smAndDown">
                    fact_check
                </v-icon>
                {{
                    $vuetify.breakpoint.smAndDown
                        ? ''
                        : $t('order.labels.reports')
                }}
            </v-btn>

            <v-btn
                :block="$vuetify.breakpoint.smAndDown"
                color="info"
                @click="$refs.orderTimeLogsRef.scroll()"
            >
                <v-icon v-if="$vuetify.breakpoint.smAndDown">
                    work_history
                </v-icon>
                {{
                    $vuetify.breakpoint.smAndDown
                        ? ''
                        : $t('order.buttons.time-logs')
                }}
            </v-btn>

            <v-btn
                color="default"
                outline
                :block="$vuetify.breakpoint.smAndDown"
                @click="$refs.orderTimelineDrawer.show()"
            >
                <v-icon v-if="$vuetify.breakpoint.smAndDown"> history </v-icon>
                {{
                    $vuetify.breakpoint.smAndDown
                        ? ''
                        : $t('order.buttons.show-timeline')
                }}
            </v-btn>
            <v-btn
                v-if="$vuetify.breakpoint.mdAndUp"
                :color="isOrderCancelled ? 'error' : 'primary'"
                :block="$vuetify.breakpoint.smAndDown"
            >
                {{ order?.status_text }}
                <v-divider
                    v-show="!isOrderCancelled"
                    vertical
                    class="tw-ml-0 md:tw-ml-4"
                />
                <v-icon
                    v-show="!isOrderCancelled"
                    right
                >
                    expand_more
                </v-icon>
            </v-btn>
        </v-toolbar>

        <v-container
            grid-list-xl
            fluid
            pa-0
        >
            <v-layout
                row
                wrap
            >
                <v-flex xs12>
                    <v-alert
                        type="error"
                        outline
                        :value="errorOnEmployeeStartToWork"
                        class="mb-4"
                    >
                        <!-- eslint-disable-next-line vue/no-v-html -->
                        <div v-html="errorOnEmployeeStartToWork" />
                    </v-alert>
                    <OrderTimeTrack
                        v-if="isShowTimeTracking"
                        :time-logs="timeLogs"
                        :order-id="order.id"
                        :dialog="response?.dialog"
                        :action="response?.action"
                        :time-track="timeTrack"
                    />
                    <OrderResponseDialog
                        v-if="response && !isShowTimeTracking"
                        ref="orderResponseDialogRef"
                        :order="order.id"
                        :action="response?.action"
                        :dialog="response?.dialog"
                    />
                </v-flex>
                <v-flex
                    md6
                    sm12
                >
                    <div class="tw-text-xl mb-2">Client Details</div>
                    <ClientDetailsCard
                        :client="order.client"
                        :delivery-address="order.delivery_address"
                    />
                </v-flex>
                <v-flex
                    md6
                    sm12
                >
                    <div class="tw-text-xl mb-2">
                        {{ $t('order.labels.contractor-details') }}
                    </div>
                    <ContractorDetailsCard :contractor="order.contractor" />
                </v-flex>
                <v-flex xs12>
                    <v-divider class="mb-3" />
                    <div class="tw-text-xl mb-2">Order Details</div>
                    <v-card>
                        <v-card-title primary-title>
                            <div class="headline tw-font-bold">
                                {{ order.order_number }}
                            </div>
                            <v-spacer />
                            <div>
                                {{
                                    order.order_date
                                        | toDateTimeLocal('ddd, MMM D, YYYY')
                                }}
                            </div>
                        </v-card-title>
                        <v-card-text>
                            <div class="tw-flex tw-justify-between mb-3">
                                <div class="">
                                    <v-label>
                                        {{
                                            $t(
                                                'order.labels.inputs.planned-start-date',
                                            )
                                        }}:
                                    </v-label>
                                    <div
                                        class="tw-flex tw-justify-between tw-items-center tw-gap-x-3"
                                    >
                                        {{
                                            order.planned_start_date
                                                | toDateTimeLocal(
                                                    'ddd, MMM D, YYYY',
                                                )
                                        }}
                                        <v-btn
                                            v-if="
                                                $page.props.viewer
                                                    ?.can_update_order ?? false
                                            "
                                            v-tooltip="
                                                `${$t(
                                                    'order.labels.edit-planned-start-date',
                                                )}`
                                            "
                                            icon
                                            ripple
                                            small
                                            class="ma-0"
                                            @click="
                                                $refs.editOrderPlannedDatesForm.show(
                                                    order,
                                                    ['planned_start_date'],
                                                )
                                            "
                                        >
                                            <v-icon
                                                color="info"
                                                small
                                            >
                                                border_color
                                            </v-icon>
                                        </v-btn>
                                    </div>
                                </div>
                                <div>
                                    <v-label>
                                        {{
                                            $t(
                                                'order.labels.inputs.planned-finish-date',
                                            )
                                        }}:
                                    </v-label>
                                    <div
                                        class="tw-flex tw-justify-between tw-items-center"
                                    >
                                        {{
                                            order.planned_finish_date
                                                | toDateTimeLocal(
                                                    'ddd, MMM D, YYYY',
                                                )
                                        }}
                                        <v-btn
                                            v-if="
                                                $page.props.viewer
                                                    ?.can_update_order ?? false
                                            "
                                            v-tooltip="
                                                `${$t(
                                                    'order.labels.edit-planned-end-date',
                                                )}`
                                            "
                                            icon
                                            ripple
                                            small
                                            class="ma-0"
                                            @click="
                                                $refs.editOrderPlannedDatesForm.show(
                                                    order,
                                                    ['planned_finish_date'],
                                                )
                                            "
                                        >
                                            <v-icon
                                                color="info"
                                                small
                                            >
                                                border_color
                                            </v-icon>
                                        </v-btn>
                                    </div>
                                </div>
                            </div>
                            <v-label class="mb-2">
                                <div
                                    class="tw-flex tw-justify-between tw-items-center"
                                >
                                    {{ $t('order.labels.inputs.description') }}:
                                    <v-btn
                                        v-if="
                                            $page.props.viewer
                                                ?.can_update_order ?? false
                                        "
                                        v-tooltip="
                                            `${$t(
                                                'order.labels.edit-description',
                                            )}`
                                        "
                                        icon
                                        ripple
                                        small
                                        class="ma-0"
                                        @click="
                                            $refs.editOrderDescriptionForm.show(
                                                order,
                                            )
                                        "
                                    >
                                        <v-icon
                                            color="info"
                                            small
                                        >
                                            border_color
                                        </v-icon>
                                    </v-btn>
                                </div>
                            </v-label>
                            <v-card
                                color="grey lighten-3"
                                tile
                            >
                                <VCardText>
                                    {{ order.description }}
                                </VCardText>
                            </v-card>
                        </v-card-text>
                    </v-card>

                    <v-divider class="mt-4 mb-3" />
                    <div>
                        <div class="tw-text-xl mb-2">Time Logs</div>
                        <OrderTimeLogs
                            ref="orderTimeLogsRef"
                            :total-time="order?.total_time_rendered"
                            :time-logs="timeLogs"
                        />
                    </div>

                    <VAlert
                        type="error"
                        :value="isOrderCancelled"
                        class="mt-3"
                    >
                        This order is cancelled!
                    </VAlert>
                </v-flex>
            </v-layout>
        </v-container>

        <OrderTimeline
            ref="orderTimelineDrawer"
            :timelines="timelines"
        />

        <OrderReports
            ref="orderReportsRef"
            :reports="reports"
        />

        <EditOrderPlannedDates ref="editOrderPlannedDatesForm" />

        <EditOrderDescription ref="editOrderDescriptionForm" />
    </div>
</template>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import ClientDetailsCard from './Components/ClientDetailsCard.vue'
import OrderLayout from './OrderLayout.vue'
import OrderResponseDialog from './Components/OrderResponseDialog.vue'
import ContractorDetailsCard from './Components/ContractorDetailsCard.vue'
import OrderTimeTrack from './Components/OrderTimeTrack.vue'
import OrderTimeline from './Components/OrderTimeline.vue'
import OrderTimeLogs from './Components/OrderTimeLogs.vue'
import OrderReports from './Components/OrderReports.vue'
import EditOrderPlannedDates from './Components/EditOrderPlannedDates.vue'
import EditOrderDescription from './Components/EditOrderDescription.vue'

export default {
    components: {
        ClientDetailsCard,
        ContractorDetailsCard,
        OrderResponseDialog,
        OrderTimeTrack,
        OrderTimeline,
        OrderTimeLogs,
        OrderReports,
        EditOrderPlannedDates,
        EditOrderDescription,
    },
    layout: [AuthLayout, OrderLayout],
    props: {
        order: {
            required: true,
            type: Object,
        },
        timeLogs: {
            required: false,
            type: Array,
            default: () => [],
        },
        timelines: {
            required: false,
            type: Array,
            default: () => [],
        },
        timeTrack: {
            required: true,
            type: Object,
        },
        reports: {
            required: false,
            type: Array,
            default: () => [],
        },
        viewer: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        client() {
            return this.order?.client
        },

        response() {
            return this.order?.response
        },

        isOrderCancelled() {
            return this.order.status == 2
        },

        noResponseAction() {
            return !this.order?.response?.requires
        },
        isShowTimeTracking() {
            // Order is In Progress && Response action is `Ready for Review`
            return (
                this.order?.status === 3 && this.response?.action?.value === 5
            )
        },
        errorOnEmployeeStartToWork() {
            return this.$page.props?.errors
                ?.employee_still_working_on_other_order
        },
    },

    mounted() {
        window.Echo.private(`orders.${this.order.id}`).listenToAll((e) => {
            // order.employee.started.working || order.employee.stopped.working
            if (e.includes('working')) {
                this.$inertia.reload({ only: ['timeTrack', 'timeLogs'] })
            } else {
                // .rejected.to-finish || cancelled || accepted || updated
                this.$inertia.reload({ only: ['order'] })
            }
        })
    },

    beforeDestroy() {
        window.Echo.leave(`orders.${this.order.id}`)
    },
}
</script>
<style scoped>
/* ============================================================
 * Vuetify text field height adjustment
 * Anpassung der Höhe von Vuetify-Solo-Textfeldern
 * ============================================================ */
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 30px;
}

/* ============================================================
 * Fixed toolbar below the app header
 * Fixierte Toolbar direkt unter dem App-Header
 *
 * EN:
 * We use `position: fixed` because `sticky` can fail in complex
 * Vuetify layouts (e.g. overflow/transform on parent elements).
 *
 * DE:
 * Wir nutzen `position: fixed`, weil `sticky` in komplexen
 * Vuetify-Layouts (overflow/transform bei Eltern) oft nicht greift.
 * ============================================================ */
.order-details-toolbar {
    position: fixed;
    left: 0;
    right: 0;

    /* EN/DE: Default header height (Desktop/Tablet) */
    top: 64px;

    z-index: 1100;
    background: white;
}

/* ------------------------------------------------------------
 * Mobile header height (all mobile browsers)
 * Mobile Header-Höhe (für alle mobilen Browser)
 *
 * EN: Vuetify toolbar is typically 56px on mobile.
 * DE: Vuetify Toolbar ist mobil typischerweise 56px hoch.
 * ------------------------------------------------------------ */
@media (max-width: 959px) {
    .order-details-toolbar {
        top: 56px;
    }
}

/* ------------------------------------------------------------
 * iOS Safe Area + fine tuning gap
 * iOS Safe-Area + Feintuning für den kleinen Abstand
 * ------------------------------------------------------------ */
@supports (-webkit-touch-callout: none) {
    .order-details-toolbar {
        --MOBILE_HEADER_PX: 56px;
        --GAP_PX: 10px; /* tuned for iOS Chrome */
        top: calc(
            var(--MOBILE_HEADER_PX) + env(safe-area-inset-top) - var(--GAP_PX)
        ) !important;
    }
}

/* ============================================================
 * Content spacing so content isn't hidden under the fixed toolbar
 * Abstand, damit Inhalt nicht unter der fixen Toolbar verschwindet
 * ============================================================ */
.w-full {
    padding-top: 56px; /* toolbar height */
}

/* Desktop/tablet needs a bit more because header offset is larger */
@media (min-width: 960px) {
    .w-full {
        padding-top: 56px; /* keep toolbar height; header is not part of content */
    }
}

/* iOS: add safe area to padding as well */
@supports (-webkit-touch-callout: none) {
    .w-full {
        padding-top: calc(56px + env(safe-area-inset-top));
    }
}
</style>
