<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
    >
        <v-list dense>
            <v-list-tile
                v-for="(group, index) in groups"
                :key="index"
                @click="submit(group.name)"
            >
                <v-list-tile-title>
                    {{ group.name }}
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>
<script>
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        subtask: null,
    }),

    computed: {
        groups() {
            return this.$page.props.groups
        },
    },

    methods: {
        show(e, subtask) {
            this.subtask = null

            e.preventDefault()

            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY

            this.subtask = subtask
            this.$nextTick(() => {
                this.isShow = true
            })
        },

        submit(group) {
            const { board } = this.$page.props
            const task = this.subtask

            this.$inertia.patch(
                this.$route('todos.boards.tasks.update', { board, task }),
                {
                    group,
                },
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'errors', 'subtasks'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            `Task has been moved to ${group}.`,
                        )
                    },
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                },
            )
        },
    },
}
</script>
