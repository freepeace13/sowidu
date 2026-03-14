<template>
    <section id="tasks">
        <v-divider></v-divider>
        <v-subheader class="px-4">Tasks</v-subheader>
        <v-container fluid class="py-0">
            <TaskCard
                v-for="task in tasks"
                :key="task.id"
                :task="task"
                class="mb-3"
                hide-members
                min-height="90"
            >
                <template slot="menu">
                    <RemoveableCardMenuList @remove="$emit('remove', task)" />
                </template>
            </TaskCard>
        </v-container>
    </section>
</template>

<script>
import RemoveableCardMenuList from './RemoveableCardMenuList';
import TaskCard from '../../Cards/TaskCard';
import { Task } from '~/services/models';

export default {
    name: 'FormsTasksList',

    components: {
        TaskCard,
        RemoveableCardMenuList
    },

    props: {
        tasks: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Task);
            }
        }
    }
}
</script>