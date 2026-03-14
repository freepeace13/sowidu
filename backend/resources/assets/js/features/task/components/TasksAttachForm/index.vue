<template>
    <section id="tasks">
        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.tasks') }}</v-subheader>
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
                    <v-list light>
                        <v-list-tile>
                            <v-list-tile-title @click="$emit('remove', task)">
                                Remove
                            </v-list-tile-title>
                        </v-list-tile>
                    </v-list>
                </template>
            </TaskCard>
        </v-container>
    </section>
</template>

<script>
import TaskCard from '../TaskCard';
import { Task } from '~/services/models';

export default {
    name: 'FormsTasksList',

    components: {
        TaskCard,
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