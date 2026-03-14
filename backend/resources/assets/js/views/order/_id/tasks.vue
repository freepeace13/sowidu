<template>
    <OrderSectionLayout
        id="order-tasks"
        title="Tasks"
        :hide-actions="!orderCopy.policies.isModifiable()"
    >
        <template slot="actions">
            <v-btn flat @click="createTask">
                Create Task
            </v-btn>
            <v-btn flat @click="browseTasks">
                <v-icon>folder</v-icon> &nbsp; Browse
            </v-btn>
        </template>

        <v-layout row wrap>
            <v-flex
                lg3 md4 sm6 xs12
                v-for="task in order.tasks"
                :key="task.id"
            >
                <TaskCard
                    :task="task"
                    :key="task.id"
                    :hide-menu="!orderCopy.policies.isModifiable()"
                >
                    <template slot="menu">
                        <v-list light>
                            <v-list-tile>
                                <v-list-tile-title
                                    @click="dispatchRemovedTask(task)"
                                >
                                    Remove
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </template>
                </TaskCard>
            </v-flex>
        </v-layout>
    </OrderSectionLayout>
</template>

<script>
import TaskCard from '~/components/UI/Cards/TaskCard';
import { Order, Task } from '~/services/models';
import { showTaskModal, showTasksSelector } from '~/services/events/modal';
import PageMixin from '../mixins/PageMixin';
import OrderSectionLayout from '../components/OrderSectionLayout';

export default {
    name: 'EditOrdersTasks',

    mixins: [ PageMixin ],

    components: {
        TaskCard,
        OrderSectionLayout
    },

    methods: {
        dispatchInsertedTask(task) {
            const tasks = Task
                .collection(this.order.tasks)
                .insert(task)
                .all();

            this.$emit('update-tasks', tasks);
        },

        dispatchRemovedTask(task) {
            const tasks = Task
                .collection(this.order.tasks)
                .remove(task)
                .all();

            this.$emit('update-tasks', tasks);
        },

        browseTasks() {
            showTasksSelector({
                selected: this.order.tasks,
                onSelect: (response) => {
                    this.dispatchInsertedTask(response.value);
                    response.close();
                }
            })
        },

        createTask() {
            showTaskModal({
                onSuccess: (response) => {
                    this.dispatchInsertedTask(response.value);
                    response.close();
                }
            });
        }
    }
}
</script>
