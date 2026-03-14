<template>
    <v-flex
        xs12
        pa-0
        class="task-group"
    >
        <v-card
            class="mx-auto"
            color="grey lighten-3 black--text"
            max-width="400"
        >
            <v-toolbar
                flat
                dense
                class="card-toolbar"
            >
                <v-toolbar-title>
                    <TaskGroupName
                        ref="taskGroupName"
                        :group="group"
                    />
                </v-toolbar-title>
                <v-spacer />
                <TaskGroupMenu
                    @click:add-card="
                        (e) => {
                            cardForm.isShow = true
                            $nextTick(() => {
                                $refs.taskForm.$refs.cardInputTitle.focus()
                            })
                        }
                    "
                    @click:duplicate="
                        (e) => {
                            log('duplicate')
                        }
                    "
                    @click:sort="
                        (e) => {
                            log('sort')
                        }
                    "
                    @click:move-all-card="
                        (e) => {
                            log('move-all-card')
                        }
                    "
                    @click:delete-all-card="
                        (e) => {
                            log('delete-all-card')
                        }
                    "
                    @click:edit="$refs.taskGroupName.edit()"
                    @click:delete="deleteGroup"
                />
            </v-toolbar>
            <div class="task-group pt-2">
                <Container
                    group-name="tasks"
                    :get-child-payload="getTaskPayload(group.order)"
                    @drop="
                        (dropResult) =>
                            $emit('task-drop', {
                                groupId: group.order,
                                dropResult,
                            })
                    "
                >
                    <Draggable
                        v-for="task in group?.tasks ?? []"
                        :key="task.id"
                    >
                        <TaskCard
                            :task="task"
                            @click:task="(e) => $emit('click:task', e)"
                        />
                    </Draggable>
                </Container>

                <TaskForm
                    ref="taskForm"
                    :is-show="cardForm.isShow"
                    :group="group.name"
                    @close="cardForm.isShow = false"
                />
            </div>
            <v-card-actions v-show="!cardForm.isShow">
                <v-btn
                    block
                    depressed
                    color="grey grey--text text--lighten-5"
                    @click="addTask"
                >
                    <v-icon>add</v-icon>
                    {{ $t('labels.add-a-card') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-flex>
</template>
<script>
import { Draggable, Container } from 'vue-smooth-dnd'
import TaskCard from '../Task/TaskCard.vue'
import TaskGroupMenu from './TaskGroupMenu.vue'
import TaskForm from '../Task/TaskForm.vue'
import TaskGroupName from './TaskGroupName.vue'
import TodoPoliciesMixin from '../../Mixins/TodoPoliciesMixin'

export default {
    components: {
        Draggable,
        TaskCard,
        Container,
        TaskGroupMenu,
        TaskForm,
        TaskGroupName,
    },

    mixins: [TodoPoliciesMixin],

    props: {
        group: {
            type: Object,
            required: true,
        },
    },
    data: () => ({
        cardForm: {
            isShow: false,
            title: null,
        },
    }),

    methods: {
        getTaskPayload() {
            return (index) => this.group.tasks[index]
        },

        deleteGroup() {
            if (!this.allowedToManageGroup()) return
            this.$confirm.ask({
                title: this.$t('todo.labels.delete-group'),
                question: this.$t(
                    'todo.labels.do you want to delete this group?',
                ),
                type: this.$t('todo.labels.delete'),
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.groups.destroy', {
                            board: this.$page.props.board.id,
                            group: this.group.name,
                        }),
                        {
                            only: ['groups', 'errors'],
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    'Group has been deleted.',
                                ),
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                        },
                    )
                },
            })
        },

        addTask() {
            if (!this.allowedToManageTask()) return

            this.cardForm.isShow = true
            this.$nextTick(() => {
                this.$refs.taskForm.$refs.cardInputTitle.focus()
            })
        },
    },
}
</script>
<style scoped>
.task-group {
    overflow-y: auto;
    height: 100%;
}
</style>
