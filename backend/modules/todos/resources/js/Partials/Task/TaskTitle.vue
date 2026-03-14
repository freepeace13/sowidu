<template>
    <v-text-field
        v-model="form.title"
        :outline="isEditing"
        :loading="form.processing"
        :disabled="form.processing"
        tabindex="0"
        single-line
        full-width
        hide-details
        class="headline font-weight-bold task-title-input"
        @focus="isEditing = true"
        @focusout="isEditing = false"
        @keyup.enter="update"
    />
</template>
<script>
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    props: {
        title: {
            type: String,
            default: null,
            required: false,
        },
    },
    data: (vm) => ({
        isEditing: false,
        form: vm.$inertia.form({
            title: vm.title,
        }),
    }),

    watch: {
        isEditing(val) {
            if (!val) this.update()
        },

        title(val) {
            this.form.title = val
        },
    },

    methods: {
        update() {
            const { board, task } = this.$page.props
            this.form.patch(
                this.$route('todos.boards.tasks.update', {
                    task,
                    board,
                }),
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['task', 'errors'],
                    errorBag: 'updateBoardTask',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () => {
                        this.isEditing = false
                        this.$root.$emit('task_activities.refresh')
                    },
                },
            )
        },
    },
}
</script>
