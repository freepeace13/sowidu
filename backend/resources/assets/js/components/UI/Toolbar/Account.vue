<template>
    <!-- Profile menu dropdown -->
    <v-menu class="profile-dropdown"
        :close-on-content-click="false"
        nudge-bottom="11"
        max-width="300px"
        nudge-right="-320"
        min-width="300px"
        offset-y
        offset-x
    >
        <v-toolbar-title slot="activator" class="px-2 text-xs-center">
            <span class="body-2 hidden-sm-and-down px-2">
                {{ profile.name }}
            </span>

            <v-avatar flat size="35">
                <v-img :src="profile.avatar.url" alt="avatar" />
            </v-avatar>
        </v-toolbar-title>

        <v-card dark>
            <v-list class="pt-3 pb-3" three-line>
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <v-avatar flat size="50">
                            <v-img :src="personal.avatar.url" alt="primary avatar" />
                        </v-avatar>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ personal.name }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            Private
                            <br/>
                            <a
                                href="javascript:void(0)"
                                v-if="!personal.equals(profile)"
                                @click="logoutCompany()"
                            >
                                Sign In
                            </a>
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-divider></v-divider>

            <div class="company-list">
                <v-list class="grey darken-3" three-line>
                    <v-list-tile
                        avatar
                        v-for="account in companies"
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
                                Business
                                <br/>
                                <a
                                    href="javascript:void(0)"
                                    v-if="!profile.equals(account)"
                                    @click="loginCompany(account.id)"
                                >
                                    Sign In
                                </a>
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </div>

            <template v-if="!check('company')">
                <v-divider></v-divider>

                <v-card-text :style="{'text-align': 'center'}">
                    <a href="#" @click="createCompany">
                        Register Company
                    </a>
                </v-card-text>
            </template>



            <v-card-actions class="grey darken-2">
                <v-btn icon :to="{ name: 'settings' }">
                    <v-icon>settings</v-icon>
                </v-btn>

                <v-spacer></v-spacer>

                <v-btn
                    color="grey darken-4"
                    @click="logout"
                >
                    Sign Out
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script>
import { User, Company } from '~/services/models';
import { AUTH_GUARDS } from '~/support/constants'
import { HandlesAuthentications } from '~/components/Mixins';

const { COMPANY, USER } = AUTH_GUARDS;

export default {
    mixins: [HandlesAuthentications()],

    props: {
        personal: {
            type: User,
            required: true
        },

        profile: {
            type: [Company, User],
            required: true
        },

        companies: {
            type: Array,
            required: true,
            validator(prop) {
                return prop.every((v) => v instanceof Company);
            }
        }
    },

    methods: {
        logout() {
            this.$auth.logout(USER);
            this.$router.push({ name: 'login' });
        },

        logoutCompany() {
            this.$auth.logout(COMPANY);
        },

        loginCompany(companyId) {
            this.$auth.authenticate({
                guard: COMPANY,
                credentials: companyId
            });
        },

        createCompany() {
            if (this.check('company')) return;

            this.$modal.show({
                size: 'md',
                title: 'Create Company',
                modal: require('~/components/UI/Modals/CreateCompany').default
            });
        }
    }
}
</script>