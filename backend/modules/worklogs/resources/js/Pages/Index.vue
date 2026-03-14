<script setup>
import { useGetUserTimezone } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import WorkLogReports from '../Components/WorkLogReports.vue'
import WorkLogRow from '../Components/WorkLogRow.vue'
import { router } from '@inertiajs/vue2'
import { useDebounceFn, useInfiniteScroll, useWindowSize } from '@vueuse/core'
import axios from 'axios'
import { computed, reactive, ref, watch } from 'vue'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'
import WorkLogForm from '../Components/WorkLogForm.vue'
import WorkLogToolbar from '../Components/WorkLogToolbar.vue'

defineProps({
    employees: {
        type: Array,
        required: true,
    },

    workLogEvents: {
        type: Array,
        required: true,
    },

    filterByEvents: {
        type: Array,
        required: true,
    },
})

const { $t, $route, $confirm } = useGlobalVariables()

const listContainer = ref(null)
const timeLogs = ref([])
const pagination = reactive({
    current_page: 0,
    next_page_url: null,
    isLoading: true,
    total: 0,
    partialCount: 0,
})

const filters = ref({
    q: null,
    events: [],
    employees: [],
    dates: {
        from: null,
        to: null,
    },
    order: null,
    address: null,
    invoiceStatus: null,
})

const workLogFormRef = ref()

const headers = [
    { text: $t('labels.employees'), sortable: false },
    { text: $t('labels.order-no'), sortable: false },
    {
        text: $t('work_log.labels.delivery-address'),
        sortable: false,
    },
    {
        text: $t('work_log.labels.started-at'),
        sortable: false,
    },
    { text: $t('work_log.labels.ended-at'), sortable: false },
    {
        text: $t('work_log.labels.status'),
        sortable: false,
        width: '80px',
    },
    {
        text: $t('work_log.labels.payment_form'),
        sortable: false,
    },
    { text: $t('work_log.labels.duration'), sortable: false },
]

const showTotalRow = computed(() => pagination.total == pagination.partialCount)

watch(
    filters,
    () => {
        fetch(1)
    },
    {
        deep: true,
    },
)
const totalHours = ref(0)

const fetch = useDebounceFn(async (page = 1) => {
    try {
        pagination.isLoading = true
        if (page == 1) {
            timeLogs.value = []
            pagination.current_page = 0
            pagination.next_page_url = null
            pagination.isLoading = true
            pagination.partialCount = 0
            pagination.total = 0
        }
        const queries = filters.value
        const response = await axios.get($route('work_logs.json.get'), {
            params: {
                page,
                ...queries,
                timezone: useGetUserTimezone(),
            },
        })

        if (response && response.data) {
            const { data, current_page, next_page_url, total, per_page } =
                response.data.workLogs

            timeLogs.value = [...timeLogs.value, ...data]
            totalHours.value = response.data.totalHours
            pagination.current_page = current_page
            pagination.next_page_url = next_page_url
            pagination.total = total

            pagination.partialCount = calculatePartialCount(
                per_page,
                current_page,
                total,
            )

            responsivePaginator()
        } else {
            console.error('Invalid response structure', response)
        }
    } catch (errors) {
        console.error(errors)
    } finally {
        pagination.isLoading = false
    }
}, 500)

function attachInfiniteScroll() {
    useInfiniteScroll(
        window,
        () => {
            if (pagination.isLoading || !pagination.next_page_url) return
            fetch(pagination.current_page + 1)
        },
        { distance: 10 },
    )
}

attachInfiniteScroll()
fetch()

function responsivePaginator() {
    const { current_page, next_page_url } = pagination
    const listHeight = 740 * current_page
    const { height } = useWindowSize()
    if (height.value > listHeight) {
        if (next_page_url) {
            fetch(pagination.current_page + 1) // Fetch next page
        }
    }
}

function calculatePartialCount(per_page, current_page, total) {
    return per_page * current_page > total ? total : per_page * current_page
}

function confirmDeleting(workLog) {
    $confirm({
        title: $t('labels.delete'),
        question: $t('work_log.messages.confirm-delete'),
        type: 'delete',
        confirm: () => {
            router.delete(
                $route('work_logs.manual_entries.destroy', {
                    workLog,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        fetch()
                    },
                },
            )
        },
    })
}

function updatePaymentForm({ id, value }) {
    axios
        .patch(
            $route('work_logs.manual_entries.updatePaymentForm', {
                workLog: id,
            }),
            {
                payment_form: value,
            },
        )
        .then(() => {
            fetch()
        })
        .catch((error) => {
            console.error('Failed to update payment form', error)
        })
}
</script>
<template>
    <div class="tw-bg-bg-grey tw-h-full tw-pb-5">
        <v-container
            fluid
            pt-2
            grid-list-md
            class=""
        >
            <v-layout
                row
                wrap
                class="toolbar-container tw-bg-bg-grey"
            >
                <WorkLogToolbar
                    :model-value.sync="filters"
                    :is-loading="pagination.isLoading"
                    @click:create-work-log="() => workLogFormRef.show()"
                />
            </v-layout>
            <v-layout
                row
                wrap
                fill-height
                class="!tw-mt-28"
            >
                <v-flex
                    sm12
                    align-self-end
                    class="!tw-flex tw-justify-center tw-items-center"
                >
                    <v-subheader
                        class="tw-px-0 md:tw-px-4 md:tw-items-center tw-justify-end"
                    >
                        {{
                            $t('pagination.showing-results', { ...pagination })
                        }}
                    </v-subheader>
                    <v-spacer />

                    <v-subheader
                        class="tw-px-0 md:tw-px-4 md:tw-items-center tw-justify-end"
                    >
                        <span class="tw-font-bold tw-mr-2">{{
                            totalHours
                        }}</span>
                    </v-subheader>
                </v-flex>
                <div class="tw-flex tw-w-full tw-h-full tw-max-h-full">
                    <v-flex
                        xs12
                        class="!tw-overflow-auto !tw-grow elevation-10"
                    >
                        <v-data-table
                            id="listContainer"
                            ref="listContainer"
                            :headers="headers"
                            :items="timeLogs"
                            :loading="pagination.isLoading"
                            class=""
                            hide-actions
                            item-key="id"
                            :expand="true"
                        >
                            <template #items="itemProps">
                                <WorkLogRow
                                    :work-log="itemProps.item"
                                    :is-show-can-edit="itemProps.item.can_edit"
                                    :is-show-can-delete="
                                        itemProps.item.can_delete
                                    "
                                    @click:show-reports="
                                        itemProps.expanded = !itemProps.expanded
                                    "
                                    @click:edit="
                                        (workLog) =>
                                            $refs.workLogFormRef.show(workLog)
                                    "
                                    @click:delete="
                                        (workLog) => confirmDeleting(workLog)
                                    "
                                    @update-payment-form="updatePaymentForm"
                                />
                            </template>
                            <template #expand="showMoreProps">
                                <WorkLogReports
                                    :reports="showMoreProps.item?.reports ?? []"
                                />
                            </template>
                            <template #footer>
                                <tr
                                    v-for="n in 4"
                                    v-show="pagination.isLoading"
                                    :key="n"
                                >
                                    <td
                                        v-for="i in headers.length"
                                        :key="i"
                                    >
                                        <div
                                            class="tw-w-full tw-divide-y tw-rounded tw-animate-pulse tw-mt-3"
                                        >
                                            <div
                                                class="tw-h-4 tw-bg-gray-300 tw-rounded-full tw-w-full tw-mb-2.5"
                                            />
                                        </div>
                                    </td>
                                </tr>
                                <tr
                                    v-show="
                                        showTotalRow && !pagination.isLoading
                                    "
                                >
                                    <td
                                        class="text-xs-right mr-4"
                                        :colspan="headers.length - 1"
                                    >
                                        <strong>
                                            {{ $t('labels.total') }}:
                                        </strong>
                                    </td>
                                    <td class="font-weight-bold">
                                        {{ totalHours }}
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
                    </v-flex>
                </div>
            </v-layout>
        </v-container>
        <WorkLogForm
            ref="workLogFormRef"
            :employees="$page.props?.employees ?? []"
            @refresh="fetch"
        />
        <div
            class="tw-fixed tw-bottom-0 tw-border-t tw-flex tw-justify-end tw-bg-white tw-py-4 tw-px-8 tw-w-full"
        >
            <div class="font-weight-bold">
                {{ totalHours }}
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.toolbar-container {
    position: fixed;
    left: 18px;
    right: 18px;
    z-index: 10;
    top: 64px;
}

#listContainer {
    // max-height: 77vh;
    // overflow: auto;

    // .v-table__overflow {
    //     .v-datatable {
    //         position: relative;

    //         thead {
    //             tr {
    //                 th {
    //                     position: sticky;
    //                     top: 0;
    //                     background: #fff;
    //                 }
    //             }
    //         }
    //     }
    // }
    .v-table__overflow {
        .v-datatable {
            position: relative;

            thead {
                tr {
                    th {
                        position: sticky;
                        top: 0;
                        background: #fff;
                    }
                }
            }
        }
    }
}
</style>
