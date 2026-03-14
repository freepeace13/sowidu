<template>
    <v-dialog
        v-model="isShow"
        scrollable
    >
        <v-card>
            <v-toolbar
                dense
                flat
                card
                prominent
                class="py-2"
            >
                <v-toolbar-title>
                    Time Logs for
                    <span
                        class="font-weight-bold tw-underline tw-cursor-pointer"
                        @click="isShow = false"
                    >
                        {{ task?.title }}
                    </span>
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-card-text>
                <v-container
                    grid-list-md
                    fluid
                    pa-2
                >
                    <v-layout wrap>
                        <v-flex
                            xs12
                            class="tw-flex"
                        >
                            <v-switch
                                v-model="filter.groupByUser"
                                label="Group by User"
                                color="primary"
                                hide-details
                            />
                            <v-spacer />
                            <v-btn
                                color="primary"
                                dark
                                class="tw-min-w-[32px]"
                                @click="$emit('open:form')"
                            >
                                <v-icon :left="$vuetify.breakpoint.smAndUp"
                                    >more_time</v-icon
                                >
                                {{ $vuetify.breakpoint.xs ? '' : 'Log Time' }}
                            </v-btn>
                        </v-flex>
                        <v-flex xs12>
                            <v-data-table
                                :headers="isGrouped ? groupHeaders : headers"
                                :items="timeLogs"
                                :loading="isLoading"
                                :pagination.sync="pagination"
                                :total-items="pagination.totalItems"
                                class="elevation-5"
                            >
                                <template #items="{ item }">
                                    <td>
                                        <div
                                            class="tw-flex tw-items-center tw-gap-x-2"
                                        >
                                            <Subscriber
                                                :avatar="item.user.photo"
                                            />
                                            {{ item.user.name }}
                                        </div>
                                    </td>
                                    <td>{{ item.duration_text }}</td>
                                    <td v-show="!isGrouped">{{ item.date }}</td>
                                    <td class="text-truncate description">
                                        <div
                                            v-show="!isGrouped"
                                            v-tooltip="{
                                                content: item?.description,
                                                visible:
                                                    item.description !== null &&
                                                    item.description.length >
                                                        35,
                                            }"
                                        >
                                            {{ item.description ?? '-' }}
                                        </div>
                                        <div v-show="isGrouped">
                                            Total hours rendered.
                                        </div>
                                    </td>
                                    <td
                                        v-show="!isGrouped"
                                        width="150"
                                    >
                                        {{
                                            item.created_at
                                                | formatDate('MMM DD, YYYY')
                                        }}
                                        {{ item.created_at | formatDate('LT') }}
                                    </td>
                                    <td v-show="!isGrouped">
                                        <v-icon
                                            v-if="item.is_owned"
                                            small
                                            class="mr-2"
                                            color="blue"
                                            @click="
                                                $emit('open:form', {
                                                    id: item.id,
                                                    duration:
                                                        item.duration_text,
                                                    date: item.date,
                                                    description:
                                                        item.description,
                                                })
                                            "
                                        >
                                            edit
                                        </v-icon>
                                        <v-icon
                                            v-if="item.is_owned"
                                            small
                                            color="red"
                                            @click="deleteTimeLog(item)"
                                        >
                                            delete
                                        </v-icon>
                                    </td>
                                </template>
                                <template #footer>
                                    <td class="text-xs-right">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="font-weight-bold">
                                        {{ totalHours.text }}
                                    </td>
                                </template>
                            </v-data-table>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="blue darken-1"
                    flat
                    @click="isShow = false"
                >
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import axios from 'axios'
import Subscriber from '../../Subscriber/Subscriber.vue'

export default {
    components: { Subscriber },
    data: () => ({
        isShow: false,
        isLoading: false,
        timeLogs: [],
        pagination: {
            page: 1,
            rowsPerPage: 10,
            totalItems: 0,
            descending: false,
            sortBy: null,
        },
        task: null,
        headers: [
            { text: 'User', sortable: false },
            { text: 'Duration', value: 'duration' },
            { text: 'Log Date', value: 'date' },
            { text: 'Description', sortable: false },
            { text: 'Date Created', value: 'created_at' },
            { text: 'Actions', sortable: false },
        ],
        groupHeaders: [
            { text: 'User', sortable: false },
            { text: 'Duration', value: 'duration' },
            { text: 'Description', sortable: false },
        ],
        totalHours: {
            original: 0,
            text: '0h',
        },
        filter: {
            groupByUser: false,
        },
    }),

    computed: {
        isGrouped() {
            return this.filter.groupByUser
        },
    },

    watch: {
        pagination: {
            handler({ rowsPerPage, descending, page, sortBy }, oldFilters) {
                if (
                    rowsPerPage != oldFilters.rowsPerPage ||
                    descending != oldFilters.descending ||
                    page != oldFilters.page ||
                    sortBy != oldFilters.sortBy
                ) {
                    this.fetch({ rowsPerPage, descending, page, sortBy })
                }
            },
            deep: true,
        },

        filter: {
            handler(filter) {
                this.fetch({ ...this.pagination, ...filter })
            },
            deep: true,
        },

        isShow(newVal) {
            if (!newVal) {
                // Reset fitlers and pagination
                this.task = null
                this.pagination = {
                    page: 1,
                    rowsPerPage: 10,
                    totalItems: 0,
                    descending: false,
                    sortBy: null,
                }
                this.totalHours = {
                    original: 0,
                    text: '0h',
                }
                this.filter = {
                    groupByUser: false,
                }
            }
        },
    },

    mounted() {
        this.$root.$on('todo.board.time_logs.refresh', this.fetch)
    },

    beforeDestroy() {
        this.$root.$off('todo.board.time_logs.refresh', this.fetch)
    },

    methods: {
        show(task = null) {
            this.isShow = true
            this.task = task
            this.fetch()
        },

        async fetch(
            filters = {
                rowsPerPage: 10,
                descending: false,
                sortBy: null,
                page: 1,
            },
        ) {
            try {
                if (!this.task) return

                this.isLoading = true
                this.timeLogs = []
                const { board } = this.$page.props
                const task = this.task
                const {
                    data: {
                        data: { data, ...pagination },
                        total_hours,
                    },
                } = await axios.get(
                    this.$route('todos.boards.tasks.time-logs.index', {
                        board,
                        task,
                        ...filters,
                    }),
                )

                this.totalHours = total_hours
                this.timeLogs = data
                this.pagination = {
                    ...this.pagination,
                    page: pagination.current_page,
                    rowsPerPage: parseInt(pagination.per_page),
                    totalItems: parseInt(pagination.total),
                }
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        },

        deleteTimeLog({ id, board_id, task_id }) {
            const time_log = id
            const board = board_id
            const task = task_id

            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this time log?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.tasks.time-logs.destroy', {
                            board,
                            task,
                            time_log,
                        }),
                        {
                            preserveScroll: true,
                            preserveState: true,
                            only: ['errors'],
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    'Time log has been deleted.',
                                ),
                            onError: (errors) => {
                                this.$root.$emit('flash.validation', errors)
                            },
                            onFinish: () => this.fetch(this.pagination),
                        },
                    )
                },
            })
        },
    },
}
</script>
<style lang="css" scope>
.description {
    max-width: 35ch;
}
</style>
