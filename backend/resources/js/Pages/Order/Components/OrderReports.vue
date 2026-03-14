<template>
    <v-dialog
        v-model="isShow"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $tc('order.labels.order') }}
                    {{ $t('order.labels.reports') }}
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
                        <v-flex
                            xs12
                            :class="{
                                'px-0': $vuetify.breakpoint.smAndDown,
                            }"
                        >
                            <v-alert
                                :value="!reports.length"
                                color="info"
                                icon="info"
                                outline
                            >
                                {{ $t('order.errors.empty-reports') }}
                            </v-alert>
                            <WorkLogReport
                                v-for="report in reports"
                                :key="report.id"
                                :causer="report.user"
                                :report="report"
                            />
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
    props: {
        reports: {
            required: false,
            type: Array,
            default: () => [],
        },
    },
    data: () => ({
        isShow: false,
    }),
    methods: {
        show() {
            this.isShow = true
            this.$inertia.reload({ only: ['reports'] })
        },
        close() {
            this.isShow = false
        },
    },
}
</script>
