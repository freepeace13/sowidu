<template>
    <v-card
        ref="commentForm"
        :class="['w-full']"
        color="white"
        :hover="isHoverable"
        @click="
            () => {
                if (!isNew) return
                showForm()
            }
        "
    >
        <v-card-title :class="['grey lighten-4', { 'pb-0': isNew }]">
            <slot v-if="!isShowForm" />
            <div
                v-show="isShowForm"
                class="w-full"
            >
                <v-textarea
                    ref="messageInput"
                    v-model="form.message"
                    :loading="form.processing"
                    :error-messages="form.errors.message"
                    color="primary"
                    autofocus
                    box
                    value=""
                    auto-grow
                    outline
                    full-width
                    placeholder="Write a comment..."
                    :row-height="5"
                    class="body-2 font-weight-regular w-full"
                />
            </div>
        </v-card-title>
        <v-divider />
        <v-card-actions v-show="isShowForm">
            <v-spacer />
            <v-btn
                small
                depressed
                :disabled="form.processing"
                color="secondary"
                class="mr-0"
                @click.stop="closeForm"
            >
                Cancel
            </v-btn>
            <v-btn
                small
                depressed
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                class="mr-0"
                @click="submit"
            >
                Save
                <template #loader>
                    <span>Saving...</span>
                </template>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>
<script>
import { onClickOutside } from '@vueuse/core'
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    props: {
        isNew: {
            type: Boolean,
            default: false,
        },

        isHoverable: {
            type: Boolean,
            default: true,
        },
    },

    data: (vm) => ({
        isShowForm: false,
        form: vm.$inertia.form({
            message: '',
        }),
    }),

    mounted() {
        onClickOutside(this.$refs.commentForm, () => this.closeForm())
    },

    methods: {
        showForm(comment = null) {
            if (comment) {
                this.form.id = comment.id
                this.form.message = comment.message
            }

            this.isShowForm = true
            this.$nextTick(() => {
                this.$refs.messageInput.focus()
            })
        },

        closeForm() {
            this.isShowForm = false
            this.form.clearErrors()
            this.form.reset()
            this.$emit('close')
        },

        submit() {
            const isPost = this.isNew
            const comment = this.form?.id
            const { board, task } = this.$page.props
            const route = this.$route(
                `todos.boards.tasks.comments.${isPost ? 'store' : 'update'}`,
                { board, task, comment },
            )

            this.form[isPost ? 'post' : 'patch'](route, {
                ...socketIdHeader,
                preserveScroll: true,
                only: ['errors'],
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        'Comment has been created.',
                    )
                    this.$root.$emit('task_activities.refresh')
                    this.closeForm()
                },
                onError: (errors) => {
                    console.log('errors', errors)
                },
            })
        },
    },
}
</script>
