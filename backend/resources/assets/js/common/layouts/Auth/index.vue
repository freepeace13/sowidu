<template>
    <v-app dark>
        <template v-if="!$store.getters['auth/isProperlyAuthenticated']">
            <v-container fluid fill-height>
                <v-layout justify-center align-center fill-height>
                    <SuspensionSpinner />
                </v-layout>
            </v-container>
        </template>

        <template v-else>
            <LockScreen>
                <v-app dark>
                    <Header :title="$route.meta.title">
                        <template v-slot:side-icon>
                            <v-btn icon
                                v-if="isInnerView"
                                @click="isInnerView
                                    ? $router.back()
                                    : $refs['account-drawer'].toggle()"
                            >
                                <v-icon v-html="isInnerView ? 'chevron_left' : 'apps'" />
                            </v-btn>
                        </template>
                        <template v-slot:additional-items>
                            <LocaleSetter />

                            <NotificationOverview
                                @read-notification="handleReadNotification"
                            />

                            <AccountOverview
                                @login="handleLogin"
                                @logout="handleLogout"
                                @create-company="handleCreateCompany"
                            />
                        </template>

                        <template v-slot:extension>
                            <router-view name="header"></router-view>
                        </template>
                    </Header>

                    <AccountOverviewDrawer v-if="isInnerView" ref="account-drawer" />
                    <Resumable />
                    <LightboxCarousel />
                    <Snackbar />
                    <Modal />
                    <Alert />
                    <NotificationSnackbar />

                    <v-content>
                        <ErrorBoundary>
                            <Suspense
                                :delay="200"
                                class="fill-height"
                                @rejected="handleErrors"
                            >
                                <SuspensionSpinner slot="fallback" />
                                
                                <RetrievePermissions>
                                    <v-container fluid grid-list-lg
                                        fill-height
                                        justify-start
                                        align-start
                                        align-content-start
                                    >
                                        <v-layout justify-start align-start fill-height>
                                            <router-view></router-view>
                                        </v-layout>
                                    </v-container>
                                </RetrievePermissions>
                            </Suspense>
                        </ErrorBoundary>
                    </v-content>
                </v-app>
            </LockScreen>
        </template>
    </v-app>
</template>

<script>
import HandlesAuthentications from '~/components/Mixins/HandlesAuthentications';
import Header from '@common/components/Header';
import AccountOverviewDrawer from '@features/account/containers/AccountOverviewDrawer';
import Snackbar from '@common/components/Snackbar';
import Modal from '@common/components/Modal';
import Alert from '@common/components/Alert';
import NotificationSnackbar from '@features/notification/components/Snackbar';
import Resumable from '@libs/vue-resumable/components/v-resumable';
import LightboxCarousel from '@libs/v-lightbox/components/v-lightbox-carousel';
import LockScreen from '@features/auth/containers/LockScreen';
import SuspensionSpinner from '@common/components/SuspensionSpinner';
import AccountOverview from '@features/account/containers/AccountOverview';
import NotificationOverview from '@features/notification/containers/NotificationOverview';
import ErrorBoundary from '@app/components/ErrorBoundary';
import Vue from 'vue';
import LocaleSetter from '@app/containers/LocaleSetter';
import RetrievePermissions from '@features/auth/containers/RetrievePermissionsBoundary';

export default {
    data: () => ({
        error: {},
    }),

    mixins: [HandlesAuthentications()],

    components: {
        Header,
        LocaleSetter,
        AccountOverview,
        NotificationOverview,
        AccountOverviewDrawer,
        Resumable,
        LightboxCarousel,
        Snackbar,
        Modal,
        Alert,
        NotificationSnackbar,
        SuspensionSpinner,
        LockScreen,
        ErrorBoundary,
        RetrievePermissions
    },

    computed: {
        isInnerView() {
            return this.$route.name !== 'desktop';
        },
    },

    methods: {
        handleErrors(errors) {
            throw errors;
        },

        drawer(state, value) {
            this.$refs['account-drawer'][state] = (value === undefined)
                ? !this.$refs['account-drawer'][state]
                : value;
        },

        handleLogin({ guard, credentials }) {
            if (guard === 'user') {
                this.$auth.logout('company');
                return;
            }

            this.$auth.authenticate({ guard, credentials });
        },

        handleLogout(guard) {
            this.$auth.logout(guard);

            if (guard === 'user') {
                this.$router.push({ name: 'login' });
            }
        },

        handleReadNotification(notification) {
            this.$notification
                .read(notification.id)
                .then(() => this.$router.replace(notification.route()));
        },

        handleCreateCompany() {
            if (this.check('company')) return;

            this.$modal.show({
                size: 'md',
                title: 'Create Company',
                modal: require('~/components/UI/Modals/CreateCompany').default
            });
        }
    },
}
</script>

<style lang="scss" scoped>
    @import './styles.scss';
</style>