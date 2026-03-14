export default {
    computed: {
        isImpersonating() {
            return this.$page.props.user.impersonating
        },

        isImpersonated() {
            return (company) => this.$page.props.user.tenant.id === company.id
        },
    },

    methods: {
        impersonateEnter(company) {
            this.$inertia.post(
                this.$route('account.organizations.authorize', {
                    _query: { token: company.uuid },
                }),
            )
        },

        impersonateLeave() {
            this.$inertia.delete(this.$route('account.organizations.logout'))
        },
    },
}
