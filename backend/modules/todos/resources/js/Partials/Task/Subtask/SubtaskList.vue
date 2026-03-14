<template>
    <v-layout
        v-if="subtasks?.length || isShow"
        column
        justify-start
    >
        <v-flex>
            <v-progress-linear
                v-if="isLoading"
                indeterminate
                class="px-3"
            />
        </v-flex>
        <v-flex
            v-show="isShow || subtasks?.length"
            class="font-weight-bold title tw-flex tw-justify-between tw-items-center"
        >
            <div>Subtasks</div>
            <v-btn
                icon
                small
                @click="$refs.subtaskForm.show()"
            >
                <v-icon>add</v-icon>
            </v-btn>
        </v-flex>
        <v-flex
            v-if="task"
            class="subtask-list"
        >
            <SubtaskForm
                ref="subtaskForm"
                @close="close"
            />
            <SubtaskItem
                v-for="subtask in subtasks"
                :key="subtask.id"
                :subtask="subtask"
            />
        </v-flex>
        <SubtaskGroupMenu ref="subtaskGroupMenu" />
    </v-layout>
</template>
<script>
import SubtaskForm from './SubtaskForm.vue'
import SubtaskItem from './SubtaskItem.vue'
import SubtaskGroupMenu from './SubtaskGroupMenu.vue'

export default {
    components: { SubtaskForm, SubtaskItem, SubtaskGroupMenu },

    props: {
        subtasks: {
            type: Array,
            required: false,
            default: () => [],
        },
        task: {
            required: true,
            type: Object,
            default: () => ({}),
        },
    },

    data: () => ({
        isShow: false,
        isLoading: false,
    }),

    methods: {
        fetch() {
            this.isLoading = true
            this.$inertia.reload({
                only: ['subtasks'],
                onFinish: () => {
                    this.isLoading = false
                    this.$root.$emit('task_attachments.refresh')
                },
            })
        },

        show() {
            this.isShow = true
            this.$nextTick(() => {
                this.$refs.subtaskForm.show()
            })
        },

        close() {
            if (!this.subtasks.length) this.isShow = false
        },
    },
}
</script>
