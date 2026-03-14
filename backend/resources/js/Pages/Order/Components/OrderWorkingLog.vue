<template>
    <div
        class="tw-grid tw-grid-cols-3 md:tw-grid-cols-5 tw-items-center tw-gap-x-2 px-3 py-3 tw-auto-rows-auto"
    >
        <div
            class="tw-flex tw-gap-x-2 md:tw-col-span-2 tw-col-span-3 tw-items-center"
        >
            <AppAvatar
                :avatar="workLog.causer?.photo"
                :name="workLog.causer?.name"
                class="tw-opacity-75"
            />
            <div class="">
                <div class="tw-font-semibold">{{ workLog.causer?.name }}</div>
                <div class="tw-text-xs tw-text-gray-400">
                    {{ workLog.causer?.role }}
                </div>
            </div>
            <div
                class="tw-text-sm tw-cursor-pointer tw-text-info tw-ml-auto md:tw-ml-5 hover:tw-underline"
                @click="$emit('click:show-report', workLog)"
            >
                Show Reports
            </div>
        </div>
        <div
            :class="[
                '',
                {
                    'tw-col-span-3 tw-text-center md:tw-text-right':
                        !canCreateReport || !canStopTime,
                    'tw-text-center md:tw-text-right tw-col-span-3 md:tw-col-span-1':
                        canCreateReport || canStopTime,
                },
            ]"
        >
            <v-chip
                label
                color="primary"
                text-color="white"
            >
                <v-icon left> timer </v-icon>
                <span class="tw-animate-pulse tw-font-bold">
                    + {{ currentDuration }}
                </span>
            </v-chip>
        </div>
        <div
            class="tw-col-span-3 md:tw-col-span-2 tw-flex tw-justify-between tw-gap-x-4"
        >
            <v-btn
                v-show="canCreateReport"
                color="purple white--text"
                class="pa-2"
                block
                @click="$emit('click:create-report', workLog)"
            >
                {{ $t('order.labels.create-report') }}
            </v-btn>
            <v-btn
                v-show="canStopTime"
                color="warning"
                class="pa-2"
                block
                @click="confirmStop"
            >
                {{ $t('order.buttons.stop-working') }}
            </v-btn>
        </div>
    </div>
</template>
<script>
import AppAvatar from '@/Components/AppAvatar.vue'
import { useGetTimeDuration } from '@/Composables/useDayJs'
import { useIntervalFn } from '@vueuse/core'
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    components: { AppAvatar },
    props: {
        orderId: {
            required: true,
            type: Number,
        },
        workLog: {
            type: Object,
            required: true,
        },
        canStopTime: {
            required: false,
            type: Boolean,
            default: false,
        },
        canCreateReport: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        interval: null,
        currentDuration: null,
    }),

    computed: {
        reports() {
            return this.workLog?.reports
        },
    },

    created() {
        this.interval = useIntervalFn(() => {
            this.currentDuration = useGetTimeDuration(this.workLog?.created_at)
        }, 1000)
    },

    beforeDestroy() {
        // prevent memory leak
        clearInterval(this.interval)
    },

    methods: {
        confirmStop() {
            const order = this.orderId
            const workLog = this.workLog

            this.$confirm.ask({
                title: 'Confirm',
                question: 'Do you want to stop time tracker?',
                type: 'warning',
                confirm: () => {
                    this.$inertia.patch(
                        this.$route('orders.time_tracker.stop', {
                            order,
                            workLog,
                        }),
                        {},
                        {
                            ...socketIdHeader,
                            preserveScroll: true,
                            only: ['timeLogs', 'errors', 'order', 'timeTrack'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Time tracker stopped!',
                                )
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
