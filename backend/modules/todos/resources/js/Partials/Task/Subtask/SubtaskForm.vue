<template>
    <v-card
        v-show="isShow"
        hover
        class="cursor-pointer"
        color="grey lighten-4"
    >
        <v-card-text>
            <v-text-field
                ref="taskTitleInput"
                v-model="form.title"
                :loading="form.processing"
                :disabled="form.processing"
                :error-messages="form.errors.title"
                tabindex="0"
                label="What needs to be done?"
                solo
                @keyup.enter="submit"
            >
                <template #append>
                    <v-icon @click="close()">clear</v-icon>
                </template>
            </v-text-field>
        </v-card-text>
    </v-card>
</template>
<script>
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            title: '',
        }),
    }),

    methods: {
        show() {
            this.isShow = true
            this.$nextTick(() => {
                this.$refs.taskTitleInput.focus()
            })
        },

        close() {
            this.form.reset()
            this.isShow = false
            this.$emit('close')
        },

        submit() {
            const { board, task } = this.$page.props
            this.form
                .transform((data) => ({
                    ...data,
                    task_id: task.id,
                }))
                .post(this.$route('todos.boards.tasks.store', { board }), {
                    ...socketIdHeader,
                    preserveScroll: true,
                    only: ['subtasks', 'errors', 'groups'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Subtask has been added.',
                        )
                        this.form.reset()
                        this.show()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
        },
    },
}
</script>
