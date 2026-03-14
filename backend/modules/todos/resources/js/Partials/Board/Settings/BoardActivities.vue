<template>
    <div>
        <v-list
            class=""
            dense
        >
            <v-list-tile class="mt-1">
                <v-list-tile-content>
                    <v-list-tile-title
                        class="grey--text text--darken-2 font-weight-bold body-1"
                    >
                        Activity
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-container
            grid-list-xl
            pt-2
        >
            <v-layout column>
                <ActivityLog
                    v-for="activity in activities"
                    v-show="activities.length"
                    :key="activity.id"
                    :activity="activity"
                    :dense-comment="true"
                    :comment-has-menu="false"
                    :show-comment-description="true"
                    pa-2
                />
                <!-- Skeleton -->
                <ActivityLogSkeleton
                    v-for="key in 4"
                    v-show="pagination.isLoading"
                    :key="key"
                />
            </v-layout>
        </v-container>
    </div>
</template>
<script>
import ActivityLog from '../../Activity/ActivityLog.vue'
import ActivityLogSkeleton from '../../Activity/ActivityLogSkeleton.vue'
import { useInfiniteScroll } from '@vueuse/core'
import useParentFinder from '@/Composables/useParentFinder'
import axios from 'axios'

export default {
    components: { ActivityLog, ActivityLogSkeleton },

    props: {
        isShow: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        activities: [],
        pagination: {
            current_page: 0,
            next_page_url: null,
            isLoading: true,
        },
    }),

    watch: {
        isShow(newVal) {
            if (newVal) {
                this.fetch()
            }
        },
    },

    mounted() {
        this.attachInfiniteScroll()
        this.$root.$on('activities.refresh', this.fetch)
    },

    beforeDestroy() {
        this.$root.$off('activities.refresh', this.fetch)
    },

    methods: {
        attachInfiniteScroll() {
            const boardSettingsDrawer = useParentFinder(
                this.$parent,
                'boardSettingsDrawer',
            )

            useInfiniteScroll(
                boardSettingsDrawer,
                () => {
                    if (
                        this.pagination.isLoading ||
                        !this.pagination.next_page_url
                    )
                        return
                    this.fetch(this.pagination.current_page + 1)
                },
                { distance: 10 },
            )
        },

        async fetch(page = 1) {
            try {
                this.pagination.isLoading = true

                if (page == 1) {
                    this.activities = []
                    this.pagination = {
                        current_page: 0,
                        next_page_url: null,
                        isLoading: true,
                    }
                }

                const { board } = this.$page.props
                const {
                    data: { data, current_page, next_page_url },
                } = await axios.get(
                    this.$route('todos.boards.activities', { board, page }),
                )
                this.activities = [...this.activities, ...data]
                this.pagination = {
                    current_page,
                    next_page_url,
                    isLoading: false,
                }
            } catch (errors) {
                console.error(errors)
            }
        },
    },
}
</script>
