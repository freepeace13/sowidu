<template>
    <v-dialog
        ref="taskViewerModal"
        v-model="show"
        persistent
        max-width="850"
        content-class="task-viewer-modal"
    >
        <v-toolbar
            dense
            flat
            card
            prominent
            class="py-2"
        >
            <v-progress-linear
                v-if="isLoading"
                :indeterminate="true"
            />
            <v-toolbar-title
                v-show="!isLoading"
                class="headline font-weight-bold tw-w-full ml-0"
            >
                <TaskTitle
                    :title="task?.title"
                    @update:task="updateTask"
                />
            </v-toolbar-title>
            <v-spacer />
            <v-btn
                icon
                @click="close"
                ><v-icon>close</v-icon></v-btn
            >
        </v-toolbar>
        <v-card
            style="position: relative"
            class="task-viewer-container pt-2"
        >
            <v-card-text class="pb-3 pt-0 px-4">
                <v-container
                    grid-list-md
                    pa-0
                >
                    <v-layout
                        v-bind="binding"
                        wrap
                    >
                        <v-flex
                            xs12
                            class="font-weight-thin tw-flex tw-justify-between"
                        >
                            <div>
                                In group
                                <span
                                    class="grey--text text--darken-1 tw-underline font-weight-bold"
                                    v-text="task?.group"
                                />
                            </div>
                            <div v-if="task?.is_subtask && parentTask">
                                Parent task:
                                <span
                                    class="primary--text tw-underline font-weight-bold tw-cursor-pointer"
                                    @click="
                                        () => {
                                            closeModal()
                                            open(parentTask.id)
                                        }
                                    "
                                >
                                    {{ parentTask?.title }}
                                </span>
                            </div>
                        </v-flex>
                        <v-flex
                            md10
                            pr-3
                            pl-2
                        >
                            <v-layout
                                column
                                wrap
                                justify-start
                            >
                                <v-flex pa-0>
                                    <v-layout
                                        row
                                        wrap
                                        pt-1
                                    >
                                        <!-- Task Label -->
                                        <v-progress-linear
                                            v-if="isLoading"
                                            :indeterminate="true"
                                            class="mb-5"
                                        />
                                        <TaskLabels
                                            v-else
                                            :task-labels="taskLabels"
                                        />

                                        <!-- Members list -->
                                        <v-progress-linear
                                            v-if="isLoading"
                                            :indeterminate="true"
                                            class="my-5"
                                        />
                                        <TaskMembers
                                            v-else
                                            :members="members"
                                        />

                                        <LabelMenu
                                            ref="labelMenu"
                                            :settings="settings"
                                            :task-labels="taskLabels"
                                            :labels="labels"
                                        />
                                        <TaskMembersMenu
                                            ref="taskMembersMenu"
                                            :board-subscribers="
                                                boardSubscribers
                                            "
                                        />
                                    </v-layout>
                                </v-flex>
                                <v-flex
                                    pa-0
                                    class="tw-grid tw-gap-y-4"
                                >
                                    <!-- Task description -->
                                    <TaskDescription
                                        v-if="!isLoading"
                                        :description="task?.description"
                                        @update:task="updateTask"
                                    />

                                    <TaskAttachments
                                        ref="attachmentsRef"
                                        :attachments="attachments"
                                        @attach="
                                            (e) =>
                                                $refs.taskAttachmentMenuForm.show(
                                                    e,
                                                )
                                        "
                                    />

                                    <SubtaskList
                                        v-if="task"
                                        ref="subtaskList"
                                        :task="task"
                                        :subtasks="subtasks"
                                    />
                                    <v-progress-linear
                                        v-if="isLoading || !subtasks"
                                        :indeterminate="true"
                                        class="my-4"
                                    />

                                    <!-- Task Activities -->
                                    <TaskActivities v-if="!isLoading" />
                                    <v-progress-linear
                                        v-else
                                        :indeterminate="true"
                                        class="my-4"
                                    />
                                </v-flex>
                            </v-layout>
                        </v-flex>
                        <v-flex md2>
                            <v-layout
                                column
                                justify-start
                            >
                                <TaskViewerSidebar
                                    :im-subscribed="
                                        members?.some(
                                            (m) => m.id == mySubscriberId,
                                        )
                                    "
                                    :is-subtask="task?.is_subtask"
                                    @click:delete="deleteTask"
                                    @click:label="
                                        (e) => $refs.labelMenu.show(e)
                                    "
                                    @click:task-members="
                                        (e) => $refs.taskMembersMenu.show(e)
                                    "
                                    @click:duplicate="duplicateTask"
                                    @click:subtask="$refs.subtaskList.show()"
                                    @click:join="
                                        () => addMember(task, mySubscriberId)
                                    "
                                    @click:attach="
                                        (e) =>
                                            $refs.attachmentsRef.$refs.menuForm.show(
                                                e,
                                            )
                                    "
                                    @click:log-time="$refs.timeLogs.showForm()"
                                    @click:time-logs="
                                        $refs.timeLogs.showTimeLogs()
                                    "
                                />
                            </v-layout>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
        </v-card>

        <SubscriberDetailsMenu
            ref="memberDetailsMenuRef"
            :z-index="204"
        />
        <TimeLogs
            ref="timeLogs"
            :task="task"
        />
    </v-dialog>
</template>

<script>
import TaskViewerSidebar from './TaskViewerSidebar.vue'
const TaskActivities = () =>
    import(
        /* webpackChunkName: 'todo-task-activities' */ './TaskActivities.vue'
    )
import TaskDescription from './TaskDescription.vue'
import TaskMembers from './Members/TaskMembers.vue'
import TaskLabels from '../Label/TaskLabels.vue'
import TaskTitle from './TaskTitle.vue'
import SubtaskList from './Subtask/SubtaskList.vue'
import TaskMemberMixin from '../../Mixins/TaskMemberMixin'
import LabelMenu from '../Label/LabelMenu.vue'
import TaskMembersMenu from './Members/TaskMembersMenu.vue'
import SubscriberDetailsMenu from '../Subscriber/SubscriberDetailsMenu.vue'
import { socketIdHeader } from '@/Composables/useSocketId'
import TaskAttachments from './Attachments/TaskAttachments.vue'
import TimeLogs from './TimeLogs/TimeLogs.vue'

export default {
    components: {
        TaskViewerSidebar,
        TaskActivities,
        TaskMembers,
        TaskDescription,
        TaskLabels,
        TaskTitle,
        SubtaskList,
        LabelMenu,
        TaskMembersMenu,
        SubscriberDetailsMenu,
        TaskAttachments,
        TimeLogs,
    },

    mixins: [TaskMemberMixin],

    props: {
        task: {
            type: Object,
            default: null,
            required: false,
        },
        subtasks: {
            required: false,
            type: Array,
            default: () => [],
        },
        attachments: {
            required: false,
            type: Array,
            default: () => [],
        },
        settings: {
            required: false,
            type: Object,
        },
        taskLabels: {
            required: false,
            type: Array,
        },
        labels: {
            required: false,
            type: Array,
        },
        members: {
            required: false,
            type: Array,
        },
        boardSubscribers: {
            required: false,
            type: Array,
        },
    },

    data: () => ({
        show: false,
        isLoading: false,
    }),

    computed: {
        binding() {
            const binding = {}
            if (this.$vuetify.breakpoint.mdAndUp) binding.row = true
            if (this.$vuetify.breakpoint.smAndDown) binding.column = true
            return binding
        },

        parentTask() {
            return this.task?.parent_task
        },
    },

    mounted() {
        if (this.$route().current('todos.boards.tasks.show') && this.task) {
            this.show = true
            setTimeout(() => {
                this.$inertia.reload({
                    only: ['groups', 'filters', 'labels', 'settings'],
                    onFinish: () =>
                        this.$root.$emit('task_attachments.refresh'),
                })
            }, 500)
            this.taskChannelListener()
        }

        this.$root.$on('todo.task_viewer.open', this.open)
    },

    beforeDestroy() {
        this.$root.$off('todo.task_viewer.open', this.open)
    },

    methods: {
        open(id) {
            this.show = true
            this.isLoading = true
            const board = this.$page.props.board
            this.$inertia.get(
                this.$route('todos.boards.tasks.show', {
                    board,
                    task: id,
                }),
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['task', 'taskLabels', 'taskMembers'],
                    onError: (errors) => {
                        console.error(errors)
                    },
                    onFinish: () => {
                        this.$refs.subtaskList.fetch()
                        this.taskChannelListener()
                        this.isLoading = false
                    },
                },
            )
        },

        closeModal() {
            this.show = false
            window.Echo.leave(`tasks.${this.task.id}`)
        },

        close() {
            const board = this.$page.props.board.id
            this.closeModal()
            this.$inertia.get(
                this.$route('todos.boards.task.tasks-group', {
                    board,
                }),
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: [
                        'groups',
                        'errors',
                        'task',
                        'subtasks',
                        'attachments',
                        'taskLabels',
                        'taskMembers',
                    ],
                    onError: (errors) => {
                        console.error(errors)
                    },
                },
            )
        },

        deleteTask() {
            const { board, task } = this.$page.props
            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this task?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.tasks.destroy', {
                            board,
                            task,
                        }),
                        {
                            ...socketIdHeader,
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Task has been deleted.',
                                )
                            },
                            onStart: () => this.closeModal(),
                        },
                    )
                },
            })
        },

        updateTask(form) {
            const { board, task } = this.$page.props
            this.$inertia.patch(
                this.$route('todos.boards.tasks.update', {
                    task,
                    board,
                }),
                form,
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['task', 'errors'],
                    errorBag: 'updateBoardTask',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                },
            )
        },

        duplicateTask() {
            const { board, task } = this.$page.props
            this.$confirm.ask({
                title: 'Duplicate',
                question: 'Do you want to duplicate this task?',
                type: 'info',
                confirm: () => {
                    this.$inertia.post(
                        this.$route('todos.boards.tasks.store', { board }),
                        task,
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['groups', 'errors'],
                            errorBag: 'createBoardTask',
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'This task has been duplicated.',
                                )
                            },
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                        },
                    )
                },
            })
        },

        taskChannelListener() {
            if (!this.task) return
            window.Echo.private(`tasks.${this.task.id}`)
                .listen('.task.comment', () => {
                    this.$root.$emit('task_activities.refresh')
                })
                .listen('.task', () => {
                    this.$inertia.reload({ only: ['groups'] })
                })
                .listen('.task.attachment', () => {
                    this.$inertia.reload({ only: ['attachments'] })
                })
        },
    },
}
</script>
<style scoped>
.task-viewer-container {
    min-height: 90vh;
}
</style>
