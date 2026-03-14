<template>
    <div class="todo">
        <BoardToolbar
            :board="board"
            :boards="boards"
            :subscribers="subscribers"
            :labels="labels"
            :filters="filters"
            @click:settings="(e) => $refs.boardSettingsMenu.show()"
        />
        <v-divider />
        <div class="board-canvas">
            <v-container
                fluid
                grid-list-xl
                class="tw-relative tw-z-10"
            >
                <v-layout
                    row
                    align-start
                    justify-start
                >
                    <Container
                        orientation="horizontal"
                        group-name="groups"
                        drag-handle-selector=".card-toolbar"
                        :get-child-payload="(index) => getGroupPayload(index)"
                        :tag="{ value: 'div', props: { class: '!tw-flex' } }"
                        @drop="groupDropped"
                    >
                        <Draggable
                            v-for="group in groups"
                            :key="group.order"
                            class="pr-4"
                        >
                            <TaskGroup
                                :group="group"
                                @task-drop="taskDropped"
                                @click:task="
                                    (id) =>
                                        $root.$emit('todo.task_viewer.open', id)
                                "
                            />
                        </Draggable>
                        <TaskGroupForm class="!h-auto" />
                    </Container>
                    <!-- New Group Form -->
                </v-layout>
            </v-container>
        </div>
        <TaskViewer
            ref="taskViewer"
            :task="task"
            :subtasks="subtasks"
            :attachments="attachments"
            :settings="settings"
            :task-labels="taskLabels"
            :labels="labels"
            :members="taskMembers"
            :board-subscribers="subscribers"
        />
        <BoardSettingsDrawer
            ref="boardSettingsMenu"
            :settings="settings"
            :policies="policies"
            :details="details"
        />
        <SubscriberDetailsMenu
            ref="subscriberDetailsMenuRef"
            :task="task"
        />
    </div>
</template>
<script>
import { Container, Draggable } from 'vue-smooth-dnd'
import DraggerMixin from '../../Mixins/DraggerMixin'
import BoardToolbar from '../../Partials/Board/BoardToolbar.vue'
import BoardSettingsDrawer from '../../Partials/Board/Settings/BoardSettingsDrawer.vue'
import TaskGroup from '../../Partials/Group/TaskGroup.vue'
import TaskGroupForm from '../../Partials/Group/TaskGroupForm.vue'
import SubscriberDetailsMenu from '../../Partials/Subscriber/SubscriberDetailsMenu.vue'

const TaskViewer = () =>
    import(
        /* webpackChunkName: 'task-viewer' */ '../../Partials/Task/TaskViewer.vue'
    )

export default {
    components: {
        BoardToolbar,
        TaskGroup,
        TaskViewer,
        BoardSettingsDrawer,
        TaskGroupForm,
        Draggable,
        Container,
        SubscriberDetailsMenu,
    },

    mixins: [DraggerMixin],

    props: {
        groups: {
            type: Array,
            default: () => [],
            required: false,
        },
        task: {
            type: Object,
            required: false,
            default: null,
        },
        board: {
            type: Object,
            default: null,
            required: false,
        },
        subscribers: {
            type: Array,
            default: () => [],
            required: false,
        },
        boards: {
            type: Array,
            default: () => [],
            required: false,
        },
        subtasks: {
            type: Array,
            default: () => [],
            required: false,
        },
        attachments: {
            type: Array,
            default: () => [],
            required: false,
        },
        settings: {
            type: Object,
            required: false,
            default: () => ({
                permissions: [],
                labels_available_colors: [],
            }),
        },
        policies: {
            type: Object,
            default: () => ({}),
            required: false,
        },
        labels: {
            required: false,
            type: Array,
            default: () => [],
        },
        details: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        taskLabels: {
            required: false,
            type: Array,
            default: () => [],
        },
        taskMembers: {
            required: false,
            type: Array,
            default: () => [],
        },
        filters: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },

    data: () => ({
        boardGroups: [],
    }),

    mounted() {
        this.boardGroups = this.groups
        this.boardChannelListener()
    },

    beforeDestroy() {
        window.Echo.leave(`boards.${this.board.id}`)
    },

    methods: {
        getGroupPayload(index) {
            const { tasks, ...group } = this.groups[index]
            return group
        },

        boardChannelListener() {
            if (!this.board) return

            window.Echo.private(`boards.${this.board.id}`).listenToAll((e) => {
                if (e.includes('.board.')) {
                    this.$inertia.reload({ only: ['groups'] })
                }
            })
        },
    },
}
</script>
<style lang="scss" scoped>
.board-canvas {
    overflow-x: scroll;
    overflow-y: hidden;
    padding-top: 64px;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;

    .container {
        display: flex;
        flex: 1 1 0%;
        height: 100%;

        .smooth-dnd-container {
            height: 100%;
        }
    }
}

@screen xs {
    .board-canvas {
        padding-top: 110px !important;
    }
}

.task-group {
    min-width: 290px;
}
</style>
