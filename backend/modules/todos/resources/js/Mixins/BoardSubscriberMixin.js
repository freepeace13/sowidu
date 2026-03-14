export default {
    methods: {
        allowedToManageSubscribers() {
            const allowed = this.$page.props.policies.can_update_permissions
            if (!allowed)
                this.$root.$emit(
                    'flash.error',
                    this.$t(
                        'messages.only-board-admins-can-add-remove-subscribers',
                    ),
                )

            return allowed
        },

        removeBoardSubscriber(subscriber) {
            this.$confirm.ask({
                title: this.$t('headings.remove-user'),
                question: this.$t(
                    'messages.do-you-want-to-remove-user-from-this-board',
                ),
                type: this.$t('todo.labels.delete'),
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.subscribers.destroy', {
                            board: this.$page.props.board.id,
                            subscriber,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['subscribers', 'errors', 'groups'],
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t(
                                        'messages.user-has-been-removed-from-board',
                                    ),
                                ),
                            onError: (errors) =>
                                this.$root.$emit('flash.validation', errors),
                        },
                    )
                },
            })
        },
    },
}
