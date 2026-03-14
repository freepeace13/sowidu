import AccountOverview from '../components/AccountOverview';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

const EnhancedAccountOverview = {
    extends: AccountOverview,
    mixins: [DispatchWhenTokenChanges('auth/fetchCompanies')]
};

export default {
    render(createElement) {
        return createElement(EnhancedAccountOverview, {
            on: { ...this.$listeners },
            props: {
                ...this.$attrs,
                isAuthenticated: this.$store.getters['auth/isAuth'],
                isAuthenticating: false,
                authenticatedAccount: this.$store.getters['auth/profile'](),
                privateAccount: this.$store.getters['auth/profile']('user'),
                companyAccounts: this.$store.state.auth.companies,
                allowCompanyCreate: !this.$store.getters['auth/check']('company'),
            },
        });
    }
}