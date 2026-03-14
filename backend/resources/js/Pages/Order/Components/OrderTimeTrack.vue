<template>
    <v-alert
        :value="true"
        color="primary"
        outline
    >
        <div
            class="tw-flex tw-flex-col md:tw-flex-row tw-items-start tw-justify-start md:tw-justify-between md:tw-items-center tw-gap-y-2 md:tw-gap-y-0"
        >
            <div class="tw-text-lg tw-flex tw-items-center">
                <VIcon
                    large
                    :color="
                        isCurrentlyWorking
                            ? 'primary'
                            : dialog?.color ?? 'warning'
                    "
                    :class="[
                        'mr-3',
                        {
                            'tw-animate-spin': isCurrentlyWorking,
                        },
                    ]"
                >
                    {{ isCurrentlyWorking ? 'settings' : dialog?.icon }}
                </VIcon>

                {{
                    $t(
                        isCurrentlyWorking
                            ? 'order.labels.order-in-progress'
                            : dialog?.message,
                    )
                }}
            </div>

            <div
                class="tw-flex tw-flex-row tw-w-full md:tw-flex-row md:tw-w-auto"
            >
                <v-btn
                    v-show="authCanStartTimeTrack"
                    color="primary"
                    @click="confirmStart"
                >
                    {{ $t('order.buttons.start') }}
                </v-btn>
                <v-btn
                    color="warning"
                    block
                    :disabled="!timeTrack.auth_can_submit_for_review"
                    @click="confirmForReview"
                >
                    {{ $t('order.buttons.ready-for-review') }}
                </v-btn>
            </div>
        </div>
        <div
            v-if="currentlyWorkingEmployees.length"
            class="tw-flex tw-flex-col mt-2 elevation-1"
        >
            <v-subheader>
                {{ $t('order.labels.currently-working') }}
            </v-subheader>

            <div>
                <OrderWorkingLog
                    v-for="workLog in currentlyWorkingEmployees"
                    :key="workLog.id"
                    :order-id="orderId"
                    :work-log="workLog"
                    :can-stop-time="
                        authCanStopTimeTrack && workLog.causer.id == authUserId
                    "
                    :can-create-report="
                        isCurrentlyWorking && workLog.causer.id == authUserId
                    "
                    @click:create-report="
                        (workLog) => $refs.logReportFormRef.show(workLog)
                    "
                    @click:show-report="
                        (workLog) => $refs.workLogReportsRef.show(workLog)
                    "
                />
            </div>
        </div>

        <LogReportForm
            ref="logReportFormRef"
            :order-id="orderId"
        />
        <WorkLogReports ref="workLogReportsRef" />
    </v-alert>
</template>

<script>
import LogReportForm from './LogReportForm.vue'
import OrderWorkingLog from './OrderWorkingLog.vue'
import { socketIdHeader } from '@/Composables/useSocketId'
import WorkLogReports from './WorkLogReports.vue'

export default {
    components: { OrderWorkingLog, LogReportForm, WorkLogReports },
    props: {
        timeLogs: {
            required: false,
            type: Array,
            default: () => [],
        },
        orderId: {
            required: true,
            type: Number,
        },
        dialog: {
            type: Object,
            default: () => ({}),
            required: false,
        },
        action: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        timeTrack: {
            required: true,
            type: Object,
        },
    },

    computed: {
        lastLog() {
            return this.timeLogs[this.timeLogs.length - 1]
        },

        currentlyWorkingEmployees() {
            return this.timeLogs.filter(
                (timeLog) => timeLog.is_currently_working,
            )
        },

        isCurrentlyWorking() {
            return this.timeTrack.auth_is_currently_working
        },

        authCanStartTimeTrack() {
            return this.timeTrack.auth_can_start
        },

        authCanStopTimeTrack() {
            return this.timeTrack.auth_can_stop
        },

        authUserId() {
            return this.$page.props.user.id
        },
    },

    methods: {
        confirmStart() {
            const order = this.orderId
            this.$confirm.ask({
                title: 'Confirm',
                question: 'Do you want to start time tracker?',
                type: 'warning',
                confirm: () => {
                    this.$inertia.patch(
                        this.$route('orders.time_tracker.start', { order }),
                        {},
                        {
                            ...socketIdHeader,
                            preserveScroll: true,
                            only: ['timeLogs', 'errors', 'order', 'timeTrack'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Time tracker stared.',
                                )
                            },
                        },
                    )
                },
            })
        },

        confirmForReview() {
            if (!this.timeTrack.auth_can_submit_for_review) {
                // Alert user error
                return this.$root.$emit('flash', {
                    type: 'error',
                    message: this.$t(
                        'order.notifications.employee-still-working',
                    ),
                })
            }

            const order = this.orderId
            const { value } = this.action

            this.$confirm.ask({
                title: 'For Review',
                question:
                    'Are you sure about this? This action cannot be undone.',
                type: 'warning',
                confirm: () => {
                    this.$inertia.patch(
                        this.$route('orders.accept.response', { order }),
                        {
                            value,
                        },
                        {
                            preserveScroll: true,
                            only: [
                                'timeLogs',
                                'errors',
                                'order',
                                'flash',
                                'timeTrack',
                            ],
                        },
                    )
                },
            })
        },
    },
}
</script>
