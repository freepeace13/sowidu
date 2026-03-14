export default {
    computed: {
        mySubscriberId() {
            const { subscribers, user } = this.$page.props
            return subscribers.find((s) => s.user.id == user.id)?.id
        },
    },

    methods: {
        removeMember(
            task,
            subscriber,
            options = {
                reload: ['taskMembers'],
            },
            confirmCallback = null,
        ) {
            const { reload } = options
            const { board } = this.$page.props
            this.$confirm.ask({
                title: this.$t('headings.remove-member'),
                question: this.$t(
                    'messages.do-you-want-to-remove-user-from-task',
                ),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.tasks.members.destroy', {
                            board,
                            task,
                            subscriber,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['errors', 'task_activities', ...reload],
                            errorBag: 'addTaskMember',
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'messages.user-has-been-removed-from-task',
                                    ),
                                ),
                            onError: (errors) =>
                                this.$root.$emit('flash.error', errors),
                            onFinish: () => {
                                this.$root.$emit('task_activities.refresh')
                            },
                        },
                    )

                    confirmCallback()
                },
            })
        },

        addMember(
            task,
            subscriber_id,
            options = {
                reload: ['taskMembers'],
            },
        ) {
            const { reload } = options
            const { board } = this.$page.props
            this.$inertia.post(
                this.$route('todos.boards.tasks.members.store', {
                    task,
                    board,
                }),
                {
                    subscriber_id,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'task_activities', ...reload],
                    errorBag: 'addTaskMember',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () =>
                        this.$root.$emit(
                            'flash.success',
                            this.$t('messages.user-has-been-assigned-to-task'),
                        ),
                    onFinish: () => {
                        this.$root.$emit('task_activities.refresh')
                    },
                },
            )
        },
    },
}
