<template>
    <div class="tw-flex my-3 tw-items-center tw-gap-x-2 elevation-1 pa-2">
        <div class="tw-text-center">
            <AppAvatar
                :avatar="causer.photo"
                :name="causer.name"
                class="tw-opacity-75"
            />
            <div
                class="tw-text-primary tw-font-semibold tw-text-sm"
                v-text="causer.name"
            />
        </div>
        <div class="tw-grow mx-2 tw-self-stretch">
            {{ report.note }}
        </div>
        <div
            class="caption tw-text-xs tw-flex tw-flex-col tw-items-center tw-gap-y-2"
        >
            <div
                v-if="canToggleSharing"
                class="tw-text-xs tw-flex tw-items-center tw-gap-x-2"
            >
                <div>{{ $t('order.labels.share-report') }}</div>
                <v-switch
                    v-model="form.share_to_client"
                    hide-details
                    class="mt-0 tw-justify-center"
                    @change="(value) => toggleShareToClient(value)"
                />
            </div>
            <div
                v-tooltip.bottom="convertToDateLocal(report.created_at, 'lll')"
                class="tw-text-gray-400"
            >
                {{ report.created_at | toDateTimeLocal('ll') }}
            </div>
        </div>
    </div>
</template>
<script>
import AppAvatar from '@/Components/AppAvatar.vue'
import { useDateTimeLocal } from '@/Composables/useDayJs'

export default {
    components: { AppAvatar },

    props: {
        report: {
            type: Object,
            required: true,
        },
        causer: {
            type: Object,
            required: true,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            share_to_client: vm.report.share_to_client,
        }),
    }),

    computed: {
        canToggleSharing() {
            const user = this.$page.props.user

            if (this.$page.props?.viewer?.is_client) {
                return false
            }

            return user?.tenant.is_owner || user.id === this.causer.id
        },
    },

    mounted() {
        this.form.share_to_client = this.report.share_to_client
    },

    methods: {
        convertToDateLocal(date, format = 'lll') {
            return useDateTimeLocal(date, format)
        },

        toggleShareToClient() {
            const order = this.$page.props.order
            const workLog = this.report.work_log_id
            const report = this.report

            this.form
                .transform((data) => ({
                    ...data,
                    note: report.note,
                }))
                .patch(
                    this.$route('orders.time_tracker.report.update', {
                        order,
                        workLog,
                        report,
                    }),
                    {
                        only: ['timeLogs', 'timeTrack', 'errors', 'flash'],
                    },
                )
        },
    },
}
</script>
