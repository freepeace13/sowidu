<template>
    <v-expansion-panel id="timeLogsRef">
        <v-expansion-panel-content
            ref="expansionPanelRef"
            v-ripple="!isShown"
            @input="(val) => showTimeLogsClicked(val)"
        >
            <template #header>
                <div class="tw-font-bold tw-text-base">
                    Total hours: {{ totalTime }}
                </div>
            </template>
            <template #actions>
                <div
                    class="tw-text-sm tw-text-blue-600 hover:tw-cursor-pointer hover:tw-underline"
                >
                    <span v-show="!isShown">
                        {{ $t('order.buttons.show-more-details') }}
                    </span>
                    <span
                        v-show="isShown"
                        @click="isShown = false"
                    >
                        {{ $t('order.labels.hide-details') }}
                    </span>
                </div>
            </template>
            <v-card>
                <v-card-text>
                    <v-flex xs12>
                        <v-data-table
                            :headers="headers"
                            :items="timeLogs"
                            class="elevation-5"
                            hide-actions
                            item-key="id"
                            :expand="true"
                            :no-data-text="noTimeLogsToShow"
                        >
                            <template #items="props">
                                <WorkLogRow
                                    :work-log="props.item"
                                    :is-shareable="true"
                                    :is-expanded="props.expanded"
                                    :is-show-change-order-button="
                                        props.item.can_change_order
                                    "
                                    @click:show-reports="
                                        props.expanded = !props.expanded
                                    "
                                    @click:change-order="
                                        (workLog) =>
                                            $refs.changeOrderFormRef.show(
                                                workLog,
                                            )
                                    "
                                />
                            </template>
                            <template #expand="props">
                                <WorkLogReports
                                    :reports="props.item?.reports ?? []"
                                />
                            </template>
                            <template #footer>
                                <td
                                    class="text-xs-right mr-4"
                                    :colspan="headers.length - 1"
                                >
                                    <strong>Total: </strong>
                                </td>
                                <td class="font-weight-bold">
                                    {{ totalTime }}
                                </td>
                            </template>
                        </v-data-table>
                    </v-flex>
                </v-card-text>
            </v-card>

            <ChangeOrderForm
                ref="changeOrderFormRef"
                :refresh-props="['timeLogs', 'order']"
            />
        </v-expansion-panel-content>
    </v-expansion-panel>
</template>

<script>
import { useRippleEffect } from '@/Composables/useAnimation'
import WorkLogReports from '@Worklogs/Components/WorkLogReports.vue'
import WorkLogRow from '@Worklogs/Components/WorkLogRow.vue'
import ChangeOrderForm from '@/Pages/Order/TimeLogs/ChangeOrderForm.vue'

export default {
    components: { WorkLogRow, WorkLogReports, ChangeOrderForm },
    props: {
        totalTime: {
            type: String,
            default: '0',
            required: false,
        },
        timeLogs: {
            type: Array,
            default: () => [],
            required: false,
        },
    },
    data: () => ({
        isShown: false,
    }),

    computed: {
        headers() {
            let additional = []

            if (!this.$page.props?.viewer?.is_client) {
                additional = [
                    {
                        text: this.$t('order.labels.share-report'),
                        sortable: false,
                        width: '50',
                    },
                ]
            }

            return [
                { text: this.$t('labels.employees'), sortable: false },
                { text: this.$t('order.labels.order-number'), sortable: false },
                {
                    text: this.$t('order.labels.delivery-address'),
                    sortable: false,
                },
                {
                    text: this.$t('work_log.labels.started-at'),
                    sortable: false,
                },
                { text: this.$t('work_log.labels.ended-at'), sortable: false },
                {
                    text: this.$t('work_log.labels.status'),
                    sortable: false,
                    align: 'center',
                },
                { text: this.$t('work_log.labels.duration'), sortable: false },
                ...additional,
            ]
        },

        noTimeLogsToShow() {
            if (this.totalTime == 0) {
                return 'The contractor has not begun working yet.'
            }

            return 'Work logs have not been shared by the contractor.'
        },
    },

    methods: {
        scroll() {
            this.$vuetify.goTo('#timeLogsRef')

            useRippleEffect(this.$refs.expansionPanelRef.$el)
        },

        showTimeLogsClicked(value) {
            this.isShown = value
            if (value && !this.timeLogs.length) {
                this.$inertia.reload({
                    preserveState: true,
                    preserveScroll: true,
                    only: ['timeLogs'],
                })
            }
        },
    },
}
</script>
