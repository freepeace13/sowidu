<template>
    <v-menu class="profile-dropdown"
        :close-on-content-click="false"
        nudge-bottom="16"
        max-width="300px"
        nudge-right="-320"
        min-width="300px"
        offset-y
        offset-x
    >
        <v-toolbar-title slot="activator" class="px-2 text-xs-center">
            <span class="body-2 hidden-sm-and-down px-2">
                {{ authenticatedAccount.name }}
            </span>

            <v-avatar flat size="35">
                <v-img :src="authenticatedAccount.avatar.url" alt="avatar" />
            </v-avatar>
        </v-toolbar-title>

        <v-card color="grey darken-3">
            <v-list class="pt-3 pb-3 grey darken-3" three-line>
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <v-avatar flat size="50">
                            <v-img :src="privateAccount.avatar.url" alt="primary avatar" />
                        </v-avatar>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ privateAccount.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            <div>{{ $t('labels.private') }}</div>

                                <a
                                    :class="{ 'd-none': allowPrivateAccountLogin }"
                                    href="javascript:void(0)"
                                    @click="$emit('login', { guard: 'user' })"
                                >
                                    {{ $t('buttons.sign-in') }}
                                </a>
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-divider></v-divider>

            <div class="company-list">
                <v-list class="grey darken-4" three-line>
                    <v-list-tile
                        avatar
                        v-for="account in companyAccounts"
                        :key="account.id"
                    >
                        <v-list-tile-avatar>
                            <v-avatar flat size="40">
                                <v-img :src="account.avatar.url" alt="secondary avatar"/>
                            </v-avatar>
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ account.name }}</v-list-tile-title>
                            <v-list-tile-sub-title>
                                {{ $t('labels.business') }}
                                <br/>
                                <a
                                    href="javascript:void(0)"
                                    v-if="!authenticatedAccount.equals(account)"
                                    @click="$emit('login', {
                                        guard: 'company',
                                        credentials: account.id
                                    })"
                                >
                                    {{ $t('buttons.sign-in') }}
                                </a>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </div>

            <template v-if="allowCompanyCreate">
                <v-divider></v-divider>

                <v-card-text class="text-xs-center">
                    <a href="#" @click="$emit('create-company')">
                        {{ $t('buttons.register-company') }}
                    </a>
                </v-card-text>
            </template>

            <v-card-actions class="grey darken-3 align-center justify-center">
                <v-btn fab small depressed :to="{ name: 'settings' }">
                    <v-icon>settings</v-icon>
                </v-btn>

                <v-btn fab small depressed @click="$screen.lock()">
                    <v-icon>lock</v-icon>
                </v-btn>

                <v-btn fab small depressed @click="$emit('logout', 'user')">
                    <v-icon>power_settings_new</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
import TogglesLockScreen from '~/components/Mixins/TogglesLockScreen';
import User from '~/services/models/user';
import Company from '~/services/models/company';

export default {
    name: 'AccountOverview',

    mixins: [TogglesLockScreen()],

    props: {
        isAuthenticated: {
            type: Boolean,
            default: false
        },

        isAuthenticating: {
            type: Boolean,
            default: false
        },

        authenticatedAccount: {
            type: [User, Company],
            required: true,
            validator(prop) {
                return prop && prop.exists();
            }
        },

        privateAccount: {
            type: User,
            required: true,
            validator(prop) {
                return prop && prop.exists();
            }
        },

        companyAccounts: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => v.exists());
            }
        }
    },

    computed: {
        allowCompanyCreate() {
            return this.authenticatedAccount instanceof User;
        },

        allowPrivateAccountLogin() {
            return this.authenticatedAccount.equals(this.privateAccount);
        }
    }
}
</script>
