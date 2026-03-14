export default {
    methods: {
        allowedToManageTask() {
            const allowed = this.$page.props.policies.can_manage_task
            if (!allowed)
                this.$root.$emit(
                    'flash.error',
                    this.$t('messages.only-board-admins-can-manage-tasks'),
                )

            return allowed
        },

        allowedToManageGroup() {
            const allowed = this.$page.props.policies.can_manage_group
            if (!allowed)
                this.$root.$emit(
                    'flash.error',
                    this.$t('messages.only-board-admins-can-manage-groups'),
                )

            return allowed
        },

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

        allowedToManageLabels() {
            const allowed = this.$page.props.policies.can_manage_label
            if (!allowed)
                this.$root.$emit(
                    'flash.error',
                    this.$t('messages.only-board-admins-can-manage-labels'),
                )

            return allowed
        },
    },
}
