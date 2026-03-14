<template>
    <v-card flat>
        <v-card-text class="pt-0">
            <v-alert
                :value="!reports.length"
                color="info"
                icon="info"
                outline
            >
                {{ $t('work_log.messages.no-reports') }}
            </v-alert>
            <ul class="tw-list-disc">
                <li
                    v-for="report in reports"
                    :key="report.id"
                    class="tw-flex my-3 tw-items-center tw-gap-x-2 tw-border-b tw-border-gray-400 tw-ml-2 border-b-primary py-1"
                >
                    <div class="tw-grow mx-2">
                        {{ report.note }}
                    </div>
                    <div class="caption tw-text-xs tw-text-gray-400">
                        {{ report.created_at | toDateTimeLocal }}
                    </div>
                </li>
            </ul>
        </v-card-text>
    </v-card>
</template>
<script>
export default {
    props: {
        reports: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
