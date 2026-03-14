import axios from 'axios'
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    methods: {
        async groupDropped(dropResult) {
            try {
                const { removedIndex, addedIndex } = dropResult
                if (removedIndex !== null || addedIndex !== null) {
                    this.boardGroups = this.applyDrag(
                        this.boardGroups,
                        dropResult,
                    )

                    if (addedIndex !== null) this.movedGroupOrder(dropResult)
                }
            } catch (error) {
                this.$root.$emit('flash.error', error)
                this.$inertia.reload()
            }
        },

        movedGroupOrder(dropResult) {
            let groups = this.boardGroups.map(({ name }, order) => ({
                name,
                order: order + 1,
            }))

            const {
                payload: { name },
            } = dropResult
            const { board } = this.$page.props

            this.$inertia.patch(
                this.$route('todos.boards.groups.update', {
                    board,
                    group: name,
                }),
                { groups },
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'errors'],
                    errorBag: 'updateBoardGroup',
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                    onFinish: () => this.$inertia.reload({ only: ['groups'] }),
                },
            )
        },

        taskDropped(e) {
            try {
                const { groupId, dropResult } = e
                const { removedIndex, addedIndex } = dropResult
                if (removedIndex !== null || addedIndex !== null) {
                    const groups = this.boardGroups
                    const group = groups.filter((g) => g.order === groupId)[0]
                    const groupIndex = groups.indexOf(group)
                    const newGroupTasks = this.applyDrag(
                        group.tasks,
                        dropResult,
                    )
                    groups[groupIndex].tasks = newGroupTasks
                    this.boardGroups = groups

                    if (addedIndex !== null)
                        this.patchTask(dropResult.payload, group)
                }
            } catch (error) {
                this.$root.$emit('flash.error', error)
                this.$inertia.reload()
            }
        },

        // Update task - move to new `group` via API
        async patchTask(task, { name }) {
            try {
                const board = this.$page.props.board.id
                await axios.patch(
                    this.$route('todos.boards.tasks.update', {
                        task,
                        board,
                    }),
                    { group: name },
                    socketIdHeader,
                )
            } catch ({
                response: {
                    data: { message },
                },
            }) {
                this.$root.$emit('flash.error', message)
                this.$inertia.reload()
            }
        },

        applyDrag(arr, dragResult) {
            const { removedIndex, addedIndex, payload } = dragResult

            if (removedIndex === null && addedIndex === null) return arr

            const result = [...arr]
            let itemToAdd = payload

            if (removedIndex !== null) {
                itemToAdd = result.splice(removedIndex, 1)[0]
            }

            if (addedIndex !== null) {
                result.splice(addedIndex, 0, itemToAdd)
            }

            return result
        },
    },
}
