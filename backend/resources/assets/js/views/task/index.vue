<template>
    <v-layout row>
        <v-flex xs12>
            <v-layout row wrap class="fit-height">
                <GroupPanel
                    v-for="state in groups[group]"
                    :key="state.key"
                    :name="state.title"
                    :color="state.color"
                >
                    <TaskCard
                        class="mb-2"
                        v-for="task in $options.filters.whereState(tasks, state.key)"
                        :key="task.id"
                        :task="task"
                        @clickmenu-edit="viewTask(task.id)"
                        @click:title="viewTask(task.id)"
                    />
                </GroupPanel>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
import HandlesAuthorizations from '~/components/Mixins/HandlesAuthorizations';
import UseTaskStore from '~/components/Mixins/UsesTaskStore';
import Task from '~/services/models/task';
import { ProgressState } from '~/services/models/task/states';
import TaskCard from '~/components/UI/Cards/TaskCard';
import GroupPanel from './components/GroupPanel';
import { showTaskModal } from '~/services/events/modal';
import OpenState from '~/services/models/task/states/OpenState';
import InProgressState from '~/services/models/task/states/InProgressState';
import FinishedState from '~/services/models/task/states/FinishedState';
import ArchivedState from '~/services/models/task/states/ArchivedState';

export default {
    name: 'TaskIndexPage',

    mixins: [
        UseTaskStore(),
        HandlesAuthorizations()
    ],

    components: {
        GroupPanel,
        TaskCard,
    },

    filters: {
        whereState: (tasks, ...states) => {
            return tasks.filter((v) => v.policies.states('state').isOneOf(...states));
        }
    },

    computed: {
        group() {
            return Object.keys(this.groups).includes(this.$route.query.group)
                ? this.$route.query.group
                : 'pending';
        },

        groups() {
            return {
                pending: [new OpenState, new InProgressState],
                archived: [new ArchivedState],
                finished: [new FinishedState]
            };
        },

        tab() {
            const groups = ['pending', 'finished', 'archived'];

            return groups.includes(this.$route.query.group)
                ? this.$route.query.group
                : 'pending';
        }
    },

    methods: {
        viewTask(taskId) {
            this.$router.push({
                name: 'tasks',
                params: { id: taskId },
                query: this.$route.query
            }).catch(() => {
                this.$router.back();
            });
        },
    },

    watch: {
        $route: {
            immediate: true,
            handler(to) {
                this.$nextTick(() => {
                    if (typeof to.params.id !== 'undefined') {
                        showTaskModal({
                            taskId: to.params.id,
                            afterClose: () => {
                                this.$router.back();
                            }
                        });
                    }
                });
            }
        }
    }
}
</script>

<style lang="scss" scoped>
    .fit-height {
        height: calc(100% - 50px);
    }
</style>
