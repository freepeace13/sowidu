<template>
    <v-toolbar
        id="app-toolbar"
        fixed
        height="64"
        app
    >
        <v-toolbar-side-icon @click="$store.commit('ui/TOGGLE_SHORTCUT')" />

        <v-toolbar-title class="hidden-xs-only">Sowidu</v-toolbar-title>

        <v-spacer class="hidden-xs-only"></v-spacer>

        <v-toolbar-search-form
            height="45"
            :width="$vuetify.breakpoint.xsOnly ? '100%' : '50%'"
        />

        <v-spacer class="hidden-xs-only"></v-spacer>

        <v-btn icon @click="$screen.lock()" class="hidden-xs-only">
            <v-icon color="white">lock</v-icon>
        </v-btn>

        <v-notification-menu icon-color="white" />

        <Account
            v-if="authenticated && profile().exists()"
            :personal="personal"
            :profile="profile()"
            :companies="companies"
        />

        <template v-slot:extension>
            <slot></slot>
        </template>
    </v-toolbar>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import Notifications from './Notifications';
import Account from './Account';
import { AUTH_GUARDS } from '~/support/constants';
import TogglesLockScreen from '~/components/Mixins/TogglesLockScreen';
import vNotificationMenu from '~/components/notification/v-notification-menu';
import vToolbarSearchForm from './v-toolbar-search-form';

export default {
    components: {
        vToolbarSearchForm,
        vNotificationMenu,
        Notifications,
        Account,
    },

    mixins: [TogglesLockScreen()],

    computed: {
        ...mapState({
            notifications: (state) => state.notification.notifications,
            companies: (state) => state.auth.companies
        }),

        ...mapGetters('auth', {
            authenticated: 'isAuth',
            profile: 'profile',
        }),

        personal() {
            return this.profile(AUTH_GUARDS.USER);
        },

        extended() {
            console.log(this.$scopedSlots)
            return typeof this.$scopedSlots['default'] !== 'undefined';
        }
    }
}
</script>

<style lang="scss" scoped>
    // .main-toolbar {
    //     z-index: 9;
    // }
    .company-list {
        padding: 10px;
        max-height: 350px;
        overflow: auto;
        border-bottom: 1px solid #777;

        .v-list {
            > div:not(:last-child) {
                > .v-list__tile {
                    border-bottom: 1px solid #777;
                }
            }
        }
    }

    .notification-panel {
        max-height: 346px;
        overflow-y: auto;
        overflow-x: hidden;

        .notification-item {
            cursor: pointer;
            border-bottom: 1px solid #777;
        }
    }

    .shortcut-item {
        /deep/ a {
            text-decoration: none !important;
        }
        * {
            color: #fff;
        }
    }
</style>
