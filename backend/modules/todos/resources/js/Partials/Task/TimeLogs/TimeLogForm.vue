<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
    >
        <v-card>
            <v-toolbar
                dense
                flat
                card
                prominent
                class="py-2"
            >
                <v-toolbar-title class="">Log Time</v-toolbar-title>
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
                    grid-list-xs
                    fluid
                    pa-0
                >
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-text-field
                                label="Task"
                                disabled
                                prepend-icon="summarize"
                                :error-messages="form.errors.task"
                                :loading="form.processing"
                            >
                                <template #label>
                                    <TaskItem :title="task?.title" />
                                </template>
                            </v-text-field>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                :close-on-content-click="false"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.date"
                                        label="Date"
                                        prepend-icon="event"
                                        required
                                        readonly
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
                            <v-text-field
                                v-model="form.duration"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.duration"
                                label="Duration"
                                hint="Enter number of hours or minutes or both. ie: 4h 30m"
                                class="required-input"
                                required
                                placeholder="4h 30m"
                                prepend-icon="schedule"
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.description"
                                clearable
                                name="input-7-1"
                                label="Description"
                                prepend-icon="format_align_left"
                                placeholder="Enter log description..."
                                rows="3"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="secondary"
                    :disabled="form.processing"
                    @click="isShow = false"
                >
                    Cancel
                </v-btn>
                <v-btn
                    color="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Save
                    <template #loader>
                        <span>Saving...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { socketIdHeader } from '@/Composables/useSocketId'
import TaskItem from './TaskItem.vue'

export default {
    components: { TaskItem },
    props: {
        task: {
            type: Object,
            default: () => ({}),
            required: false,
        },
    },
    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            title: null,
            date: new Date().toISOString().substr(0, 10),
            duration: null,
            id: null,
            description: '',
        }),
    }),

    methods: {
        show(timeLog = null) {
            this.isShow = true

            if (timeLog) {
                this.$nextTick(() => {
                    this.form = {
                        ...this.form,
                        ...timeLog,
                    }
                })
            }
        },

        close() {
            this.form.reset()
            this.isShow = false
            this.$emit('close')
        },

        submit() {
            const { board, task } = this.$page.props
            let method = 'post'
            let route = {
                name: 'todos.boards.tasks.time-logs.store',
                params: { board, task },
            }

            if (this.form.id) {
                method = 'patch'
                route = {
                    name: 'todos.boards.tasks.time-logs.update',
                    params: { ...route.params, time_log: this.form.id },
                }
            }

            this.form.transform(({ id, ...data }) => ({
                ...data,
            }))

            this.form[method](this.$route(route.name, route.params), {
                ...socketIdHeader,
                preserveScroll: true,
                preserveState: true,
                only: ['errors'],
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        'Time log has been added.',
                    )
                    this.form.reset()

                    this.close()
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
                onFinish: () =>
                    this.$root.$emit('todo.board.time_logs.refresh'),
            })
        },
    },
}
</script>
