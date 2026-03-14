<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ workLog?.causer?.name }} {{ $t('order.labels.reports') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text class="pt-0">
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex xs12>
                            <v-list
                                two-line
                                subheader
                                class="tw-w-full"
                            >
                                <v-alert
                                    :value="!reports?.length"
                                    color="info"
                                    icon="info"
                                    outline
                                >
                                    Employee doesn't submitted any reports.
                                </v-alert>
                                <WorkLogReport
                                    v-for="report in reports"
                                    :key="report.id"
                                    :report="report"
                                    :causer="workLog.causer"
                                />
                            </v-list>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-spacer />
                <v-btn
                    outline
                    depressed
                    @click="close"
                >
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import WorkLogReport from './WorkLogReport.vue'

export default {
    components: { WorkLogReport },
    data: () => ({
        isShow: false,
        workLog: null,
    }),
    computed: {
        reports() {
            return this.workLog?.reports
        },
        userId() {
            return this.$page.props.user.id
        },
    },
    methods: {
        show(workLog) {
            this.workLog = workLog
            this.isShow = true
        },
        close() {
            this.workLog = null
            this.isShow = false
        },
    },
}
</script>
