<template>
    <v-flex
        xs12
        class="py-1"
    >
        <v-card
            :hover="true"
            class="pa-2 task-card"
            ripple
            @click="$emit('click:task', task.id)"
        >
            <v-container
                grid-list-xs
                px-2
                pa-0
                mt-2
                mb-1
                fluid
            >
                <v-layout
                    row
                    wrap
                    fill-height
                    align-content-start
                    class="task-label-container"
                >
                    <v-flex
                        v-for="label in task.labels"
                        :key="label.id"
                        xs3
                        pa-0
                        px-1
                    >
                        <TaskLabel
                            :label="label"
                            small
                        />
                    </v-flex>
                </v-layout>
            </v-container>
            <v-card-text class="py-2 pt-3 px-0">
                {{ task.title | truncate(30) }}
            </v-card-text>
            <v-card-actions class="pa-0">
                <v-icon
                    v-if="task.description"
                    small
                    color="grey"
                    class="mr-2"
                >
                    subject
                </v-icon>
                <!-- <v-icon small color="grey" class="mr-1"> attachment </v-icon>
                <span class="caption">6</span> -->
                <v-spacer />
                <Subscriber
                    v-for="member in task.members"
                    :key="member.id"
                    class="pa-0"
                    :size="8"
                    :avatar="member.photo"
                    :name="member.name"
                />
            </v-card-actions>
        </v-card>
    </v-flex>
</template>
<script>
import Subscriber from '../Subscriber/Subscriber.vue'
import TaskLabel from '../Label/TaskLabel.vue'

export default {
    components: { Subscriber, TaskLabel },
    props: {
        task: {
            type: Object,
            required: true,
        },
    },
}
</script>
