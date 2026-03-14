<template>
    <v-layout row wrap fill-height>
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
</template>

<script>
import HandlesAuthorizations from '~/components/Mixins/HandlesAuthorizations';
import UsesTaskStore from '../mixins/UsesTaskStore';
import Task from '~/services/models/task';
import TaskCard from '../components/TaskCard';
import GroupPanel from '../components/GroupPanel';
import { showTaskModal } from '~/services/events/modal';
import OpenState from '~/services/models/task/states/OpenState';
import ProgressState from '~/services/models/task/states/ProgressState';
import InProgressState from '~/services/models/task/states/InProgressState';
import FinishedState from '~/services/models/task/states/FinishedState';
import ArchivedState from '~/services/models/task/states/ArchivedState';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'IndexView',

    mixins: [
        UsesTaskStore(),
        HandlesAuthorizations(),
        DispatchWhenTokenChanges('task/all')
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
                this.$router.replace({ name: 'tasks', query: this.$route.query });
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
                                this.$router.push({
                                    name: 'tasks',
                                    query: this.$route.query
                                });
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
