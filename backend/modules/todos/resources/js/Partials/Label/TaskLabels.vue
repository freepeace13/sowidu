<template>
    <v-flex
        shrink
        mb-3
    >
        <v-layout
            column
            justify-start
            fill-height
            mr-2
        >
            <v-flex class="font-weight-bold title"> Labels </v-flex>
            <v-layout
                row
                wrap
                justify-start
                align-center
                mx-1
                class="task-label-container"
            >
                <v-flex
                    v-for="taskLabel in taskLabels"
                    :key="taskLabel.label.id"
                    pl-0
                    @click="(e) => labelMenuRef.show(e)"
                >
                    <TaskLabel :label="taskLabel.label" />
                </v-flex>
                <v-flex class="h-full pl-0">
                    <v-btn
                        v-tooltip.top="'Add label to this task'"
                        color="grey darken-4"
                        icon
                        dark
                        small
                        flat
                        @click="(e) => labelMenuRef.show(e)"
                    >
                        <v-icon x-large>new_label</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-layout>
    </v-flex>
</template>
<script>
import TaskLabel from './TaskLabel.vue'
import useParentFinder from '@/Composables/useParentFinder'

export default {
    components: { TaskLabel },

    props: {
        taskLabels: {
            type: Array,
            required: false,
        },
    },

    computed: {
        labelMenuRef() {
            return useParentFinder(this.$parent, 'labelMenu')
        },
    },
}
</script>
