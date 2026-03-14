<template>
    <v-menu
        v-model="menu"
        min-width="250"
        :close-on-content-click="false"
    >
        <template #activator="{ on }">
            <span v-on="on"><slot /></span>
        </template>

        <v-card>
            <v-toolbar
                dense
                flat
                card
                px-1
            >
                <v-toolbar-title class="body-1">Move task</v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="menu = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-divider />
            <v-list subheader>
                <v-subheader
                    class="px-4 d-flex flex-col align-items-start py-2"
                >
                    Select destination
                </v-subheader>
                <v-list>
                    <v-list-tile>
                        <v-select
                            v-model="form.group"
                            :items="items"
                            item-text="name"
                            item-value="name"
                            box
                            solo
                            class="small"
                            :loading="form.processing"
                        />
                    </v-list-tile>
                </v-list>
            </v-list>

            <v-divider class="my-2" />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    flat
                    :disabled="form.processing"
                    @click="menu = false"
                >
                    Close
                </v-btn>
                <v-btn
                    depressed
                    color="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Move
                    <template #loader>
                        <span>Moving...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>
<script>
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    data: (vm) => ({
        menu: false,
        form: vm.$inertia.form({
            group: null,
        }),
    }),

    computed: {
        items() {
            return this.$page.props?.groups
        },
    },

    watch: {
        menu(val) {
            if (val) {
                this.form.reset()
                this.form.group = this.$page.props?.task?.group
            }
        },
    },

    methods: {
        submit() {
            const { board, task } = this.$page.props
            this.form
                .transform((data) => ({
                    ...task,
                    ...data,
                }))
                .patch(
                    this.$route('todos.boards.tasks.update', { board, task }),
                    {
                        ...socketIdHeader,
                        preserveState: true,
                        preserveScroll: true,
                        errorBag: 'updateBoardTask',
                        only: ['groups', 'task', 'errors'],
                        onSuccess: () => {
                            this.$root.$emit(
                                'flash.success',
                                'Task has been moved.',
                            )
                        },
                        onError: (errors) => {
                            console.log('errors', errors)
                            this.$root.$emit('flash.validation', errors)
                        },
                        onFinish: () => {
                            this.menu = false
                            this.$root.$emit('task_activities.refresh')
                        },
                    },
                )
        },
    },
}
</script>
