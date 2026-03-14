export default {
    computed: {
        services() {
            return this.$page.props.services
        },
    },

    methods: {
        willBeShown(permission, name) {
            const { user } = this.$page.props

            // if (!user.impersonating) {
            //     // User is not impersonating, show all except "employees"
            //     return name != 'employees'
            // }

            // If user is owner then bypass all access permission.
            // if (user.tenant.is_owner) {
            //     return true
            // }

            if (name == 'account_settings') {
                return (
                    !user.impersonating ||
                    (user.impersonating && user.can['update settings'])
                )
            }

            return user.can[permission]
        },
    },
}
