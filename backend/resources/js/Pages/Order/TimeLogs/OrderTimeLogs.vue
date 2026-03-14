<script>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import WorkLogReports from '@Worklogs/Components/WorkLogReports.vue'
import WorkLogRow from '@Worklogs/Components/WorkLogRow.vue'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { computed, ref } from 'vue'
import OrderLayout from '../OrderLayout.vue'
import ChangeOrderForm from './ChangeOrderForm.vue'
import ManualTimeLogForm from './ManualTimeLogForm.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
defineProps({
    order: {
        required: true,
        type: Object,
    },
    timeLogs: {
        required: true,
        type: Array,
    },
    permissions: {
        required: true,
        type: Object,
    },
    totalTime: {
        required: true,
        type: String,
    },
})

const { $t } = useGlobalVariables()

const changeOrderFormRef = ref(null)
const manualTimeLogForm = ref(null)

const canShareTimeLogs = computed(() =>
    getPageProps('permissions.can_share_time_logs', false),
)
const canAddManualTimeLog = computed(() =>
    getPageProps('permissions.can_add_manual_time_log', false),
)

const headers = computed(() => {
    let additional = []

    if (canShareTimeLogs.value) {
        additional = [
            {
                text: $t('order.labels.share-report'),
                sortable: false,
                width: '50',
            },
        ]
    }

    return [
        { text: $t('labels.employees'), sortable: false },
        { text: $t('order.labels.order-number'), sortable: false },
        { text: $t('order.labels.delivery-address'), sortable: false },
        { text: $t('work_log.labels.started-at'), sortable: false },
        { text: $t('work_log.labels.ended-at'), sortable: false },
        {
            text: $t('work_log.labels.status'),
            sortable: false,
            align: 'center',
        },
        {
            text: $t('work_log.labels.payment_form'),
            sortable: false,
            align: 'center',
        },
        { text: $t('work_log.labels.duration'), sortable: false },
        ...additional,
    ]
})

const totalColSpan = computed(
    () => headers.value.length - (canShareTimeLogs.value ? 2 : 1),
)
</script>
<template>
    <div class="fill-height tw-w-full">
        <ChangeOrderForm
            ref="changeOrderFormRef"
            :refresh-props="['totalTime', 'timeLogs']"
        />
        <ManualTimeLogForm ref="manualTimeLogForm" />
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                id="dropdown-example"
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="'Go to order details'"
                    icon
                    class="hidden-xs-only"
                    @click="$inertia.get($route('orders.show', { order }))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $tc('labels.orders') }}
                    {{ $t('order.buttons.time-logs') }}
                </v-toolbar-title>

                <v-spacer />

                <v-btn
                    v-if="canAddManualTimeLog"
                    color="primary"
                    @click="manualTimeLogForm.show()"
                >
                    {{ $t('order.work_log.create-manual-entry') }}
                </v-btn>
            </v-toolbar>
        </portal>

        <v-container
            grid-list-lg
            text-xs-center
            px-0
            class="!tw-max-w-full"
        >
            <v-layout
                row
                wrap
            >
                <v-flex xs12>
                    <v-data-table
                        :headers="headers"
                        :items="timeLogs"
                        class="elevation-5"
                        hide-actions
                        item-key="id"
                        :expand="true"
                    >
                        <template #items="props">
                            <WorkLogRow
                                :work-log="props.item"
                                :is-shareable="true"
                                :is-expanded="props.expanded"
                                :can-share-to-client="canShareTimeLogs"
                                :is-show-change-order-button="
                                    props.item.can_change_order
                                "
                                @click:show-reports="
                                    props.expanded = !props.expanded
                                "
                                @click:change-order="
                                    (workLog) =>
                                        changeOrderFormRef.show(workLog)
                                "
                            />
                        </template>
                        <template #expand="props">
                            <WorkLogReports
                                :reports="props.item?.reports ?? []"
                            />
                        </template>
                        <template
                            v-if="canShareTimeLogs"
                            #footer
                        >
                            <td
                                class="text-xs-right mr-4"
                                :colspan="totalColSpan"
                            >
                                <strong>Total: </strong>
                            </td>
                            <td class="font-weight-bold">
                                {{ totalTime }}
                            </td>
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>
