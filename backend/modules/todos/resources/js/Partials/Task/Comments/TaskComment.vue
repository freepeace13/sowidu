<template>
    <v-flex xs12>
        <v-card flat>
            <v-card-title class="pa-0">
                <span
                    v-if="showDescription && description"
                    class="mr-1"
                    v-html="description"
                />
                <span
                    v-else
                    class="heading font-weight-bold mr-2"
                    v-text="author.name"
                />
                <span class="caption grey--text">{{ timeAgo }}</span>

                <v-spacer />

                <CommentMenu
                    v-if="comment.is_owner && hasMenu"
                    @click:edit="
                        () => {
                            $refs.commentForm.showForm(comment)
                            isShowForm = true
                        }
                    "
                    @click:delete="deleting"
                />
            </v-card-title>
            <v-card-text class="pa-0">
                <CommentForm
                    ref="commentForm"
                    :is-hoverable="comment.is_owner && hasMenu"
                    :class="[
                        'comment',
                        { 'mt-2': !dense, 'mt-1 dense': dense },
                    ]"
                    @close="isShowForm = false"
                >
                    {{ comment.message }}
                </CommentForm>
            </v-card-text>
        </v-card>
    </v-flex>
</template>
<script>
import { useTimeAgo } from '@vueuse/core'
import CommentForm from './CommentForm.vue'
import CommentMenu from './CommentMenu.vue'

export default {
    components: { CommentForm, CommentMenu },

    props: {
        comment: {
            required: false,
            type: Object,
            default: null,
        },
        dense: {
            required: false,
            type: Boolean,
            default: false,
        },
        hasMenu: {
            required: false,
            type: Boolean,
            default: true,
        },
        showDescription: {
            required: false,
            type: Boolean,
            default: false,
        },
        description: {
            required: false,
            type: String,
            default: null,
        },
    },

    data: () => ({
        isShowForm: false,
    }),

    computed: {
        author() {
            return this?.comment.author
        },

        timeAgo() {
            return useTimeAgo(this?.comment.created_at).value
        },
    },

    methods: {
        editing() {
            this.$refs.commentForm.showForm(this.comment)
            this.isShowForm = true
        },

        deleting() {
            if (!this.comment.is_owner) return
            const { board, task } = this.$page.props
            this.$confirm.ask({
                title: 'Delete comment',
                question: 'Do you want to delete your comment?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.tasks.comments.destroy', {
                            board,
                            task,
                            comment: this.comment,
                        }),
                        {
                            headers: {
                                'X-Socket-Id': window.Echo.socketId(),
                            },
                            only: ['task_activities'],
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    'Your comment has been deleted.',
                                ),
                            onFinish: () =>
                                this.$root.$emit('task_activities.refresh'),
                        },
                    )
                },
            })
        },
    },
}
</script>
