<template>
    <div class="todo">
        <v-toolbar
            color="transparent"
            flat
            class="board-toolbar"
            extended
        >
            <v-menu
                :nudge-width="100"
                allow-overflow
                max-height="400"
                offset-y
            >
                <template #activator="{}">
                    <v-toolbar-title
                        class="tw-flex tw-items-center tw-space-x-2 h-full"
                    >
                        <BoardLogo
                            :src="board?.logo"
                            :title="board?.title"
                            :is-icon="board?.is_icon"
                            :color="board?.icon_color"
                            :avatar-size="40"
                            :icon-size="28"
                        />
                        <span
                            class="text-capitalize sm:tw-text-lg xs:tw-text-sm"
                        >
                            {{ board?.title }}
                        </span>
                    </v-toolbar-title>
                </template>
            </v-menu>
        </v-toolbar>
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
                    <BoardActions
                        :manual-task-ref="$refs.manualTask"
                        :disabled-actions="disabledActions"
                    />
                </v-layout>
            </v-container>
        </div>
        <ManualTask
            ref="manualTask"
            :members="taskMembers"
            :board-subscribers="subscribers"
        />
    </div>
</template>

<script>
import ManualTask from '../Partials/Task/ManualTask/ManualTask.vue'
import BoardActions from '../Partials/Board/BoardActions.vue'
import BoardLogo from '../Partials/Board/BoardLogo.vue'

export default {
    components: {
        ManualTask,
        BoardActions,
        BoardLogo,
    },

    props: {
        groups: {
            type: Array,
            default: () => [],
        },
        board: {
            type: Object,
            default: null,
        },
        subscribers: {
            type: Array,
            default: () => [],
        },
        taskMembers: {
            type: Array,
            default: () => [],
        },
        boards: {
            type: Array,
            default: () => [],
            required: false,
        },
    },

    data: () => ({
        boardGroups: [],
        disabledActions: [],
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
