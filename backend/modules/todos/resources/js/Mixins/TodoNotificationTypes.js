export default {
    methods: {
        todoNotificationTypes() {
            const todoNotificationDir = 'App\\Notifications\\Todo'
            return {
                [`${todoNotificationDir}\\TaskCommentCreatedNotification`]: (
                    notification,
                ) => {
                    const { comment, board, ...original } = notification
                    return {
                        photo: comment.author.photo,
                        message: this.$t('todo.notifications.comment.created', {
                            author: comment.author.name,
                            task: comment.task.title,
                        }),
                        redirectTo: this.$route('todos.boards.tasks.show', {
                            board,
                            task: comment.task,
                        }),
                        ...original,
                    }
                },
                [`${todoNotificationDir}\\TaskIsMovedNotification`]: (
                    notification,
                ) => {
                    const { causer, task, board, changes, ...original } =
                        notification
                    return {
                        photo: causer.photo,
                        message: this.$t(
                            'todo.notifications.task.updated.group',
                            {
                                causer: causer.name,
                                task: task.title,
                                ...changes,
                            },
                        ),
                        redirectTo: this.$route('todos.boards.tasks.show', {
                            board,
                            task,
                        }),
                        ...original,
                    }
                },
                [`${todoNotificationDir}\\Subscriber\\SubscriberAssignedOnTaskNotification`]:
                    (notification) => {
                        const { board, task, causer, ...original } =
                            notification
                        return {
                            photo: causer.photo,
                            message: this.$t(
                                'todo.notifications.task.member.added',
                                {
                                    causer: causer.name,
                                    task: task.title,
                                    link: task?.link,
                                },
                            ),
                            redirectTo: this.$route('todos.boards.tasks.show', {
                                board,
                                task,
                            }),
                            ...original,
                        }
                    },
            }
        },
    },
}
