<template>
    <v-layout
        column
        justify-start
    >
        <v-flex
            class="font-weight-bold title mb-1 tw-my-2 tw-flex tw-flex-row tw-justify-between tw-items-center"
        >
            <div>Activity</div>
            <v-btn
                :flat="!filters.commentsOnly"
                :outline="!filters.commentsOnly"
                color="primary"
                small
                @click="toggleDetails"
            >
                {{ filters.commentsOnly ? 'Show Details' : 'Hide Details' }}
            </v-btn>
        </v-flex>
        <v-card flat>
            <v-container
                fluid
                grid-list-md
                pa-0
                pl-2
            >
                <v-layout
                    col
                    wrap
                >
                    <NewComment />

                    <v-flex
                        v-if="activities"
                        xs12
                    >
                        <ActivityLog
                            v-for="activity in activities"
                            v-show="activities"
                            :key="activity.id"
                            :activity="activity"
                            :on-task-viewer="true"
                            :avatar-size="48"
                            px-0
                            py-2
                        />
                    </v-flex>

                    <v-flex
                        v-show="isLoading"
                        xs12
                    >
                        <ActivityLogSkeleton
                            v-for="key in isLoading ? 2 : 4"
                            :key="key"
                            class="tw-mb-6"
                        />
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-layout>
</template>
<script>
import NewComment from './Comments/NewComment.vue'
import ActivityLogSkeleton from '../Activity/ActivityLogSkeleton.vue'
import ActivityLog from '../Activity/ActivityLog.vue'
import useParentFinder from '@/Composables/useParentFinder'
import { useInfiniteScroll } from '@vueuse/core'
import axios from 'axios'

export default {
    components: {
        NewComment,
        ActivityLogSkeleton,
        ActivityLog,
    },

    data: () => ({
        isLoading: false,
        activities: [],
        pagination: {
            current_page: 0,
            next_page_url: null,
        },
        filters: {
            commentsOnly: false,
        },
    }),

    mounted() {
        this.attachInfiniteScroll()
        this.$root.$on('task_activities.refresh', this.fetch)
    },

    beforeDestroy() {
        this.$root.$off('task_activities.refresh', this.fetch)
    },

    methods: {
        toggleDetails() {
            this.filters.commentsOnly = !this.filters.commentsOnly
            this.reset()
            this.$nextTick(() => {
                this.fetch()
            })
        },

        reset() {
            this.activities = []
            this.pagination = {
                current_page: 0,
                next_page_url: null,
            }
        },

        async fetch(page = 1) {
            try {
                this.isLoading = true

                const { board, task } = this.$page.props

                const url = this.$route('todos.boards.tasks.activities', {
                    board: board.id,
                    task: task.id,
                    page,
                    commentsOnly: this.filters.commentsOnly ? true : null,
                })

                const {
                    data: { data, current_page, next_page_url },
                } = await axios.get(url)

                if (page == 1) this.reset()

                this.activities = [...this.activities, ...data]
                this.pagination = {
                    current_page,
                    next_page_url,
                }
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        },

        attachInfiniteScroll() {
            const taskViewerModal = useParentFinder(this.$parent, 'dialog')
            useInfiniteScroll(
                taskViewerModal,
                () => {
                    if (this.isLoading || !this.pagination.next_page_url) return
                    this.fetch(this.pagination.current_page + 1)
                },
                { distance: 30 },
            )
        },
    },
}
</script>
