<template>
    <v-container
        fluid
        v-bind="$attrs"
    >
        <v-layout row>
            <v-flex shrink>
                <v-avatar
                    :size="avatarSize"
                    tile
                >
                    <img
                        :src="causer.photo"
                        :alt="causer.name"
                    />
                </v-avatar>
            </v-flex>
            <v-flex>
                <v-layout
                    v-if="comment"
                    row
                    wrap
                >
                    <TaskComment
                        :has-menu="commentHasMenu"
                        :comment="comment"
                        :dense="denseComment"
                        :show-description="showCommentDescription"
                        :description="description"
                    />
                </v-layout>
                <v-layout
                    v-else
                    column
                    wrap
                >
                    <v-flex
                        pb-1
                        class="activity-log"
                    >
                        <div v-html="description" />
                    </v-flex>
                    <v-flex py-0>
                        <div class="grey--text caption mb-1">
                            {{ createdAt }}
                        </div>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import { useTimeAgo } from '@vueuse/core'
import TaskComment from '../Task/Comments/TaskComment.vue'

export default {
    components: { TaskComment },

    props: {
        activity: {
            type: Object,
            required: true,
        },
        onTaskViewer: {
            type: Boolean,
            required: false,
            default: false,
        },
        avatarSize: {
            required: false,
            type: Number,
            default: 38,
        },
        denseComment: {
            required: false,
            type: Boolean,
            default: false,
        },
        commentHasMenu: {
            required: false,
            type: Boolean,
            default: true,
        },
        showCommentDescription: {
            required: false,
            type: Boolean,
            default: false,
        },
    },
    computed: {
        comment() {
            return this.activity?.comment
        },
        causer() {
            return this.activity?.causer
        },
        createdAt() {
            return useTimeAgo(this.activity?.created_at).value
        },
        hasTask() {
            return this.activity.properties?.task
        },
        description() {
            return this.getDescription()
        },
    },

    methods: {
        getDescription() {
            const { event, properties, description } = this.activity
            let key = `${description}.${event}`

            if (
                this.onTaskViewer &&
                this.$te(`${description}_viewer.${event}`)
            ) {
                key = `${description}_viewer.${event}`
            }

            return this.$t(key, {
                causer: this.causer.name,
                ...properties,
            })
        },
    },
}
</script>
