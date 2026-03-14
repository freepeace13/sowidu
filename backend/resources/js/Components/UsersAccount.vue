<template>
    <v-layout column>
        <v-layout
            class="tw-p-3"
            column
            justify-center
            align-center
            fill-height
        >
            <v-avatar size="70">
                <v-img
                    :src="user.impersonating ? user.tenant.photo : user.photo"
                    width="70"
                />
            </v-avatar>

            <div class="py-2 text-xs-center">
                <h2>
                    {{ user.impersonating ? user.tenant.name : user.name }}
                </h2>
            </div>
        </v-layout>

        <v-divider />
        <LocaleSwitcher
            block
            :lang="locale"
            :languages="locales"
        />
        <v-divider />

        <v-card
            max-height="200"
            flat
            style="overflow-y: auto"
        >
            <v-list
                class="py-2"
                two-line
                dense
            >
                <div
                    v-if="!companies.length"
                    class="text-xs-center"
                >
                    No other account(s)
                </div>

                <v-list-tile
                    v-if="user.impersonating"
                    @click="impersonateLeave"
                >
                    <v-list-tile-avatar>
                        <v-avatar size="35">
                            <v-img :src="user.photo" />
                        </v-avatar>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title class="body-2">
                            {{ user.name }}
                        </v-list-tile-title>

                        <v-list-tile-sub-title>
                            {{ $t('labels.personal-account') }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-list-tile
                    v-for="company in companies"
                    :key="company.id"
                    :class="{ 'grey lighten-5': isImpersonated(company) }"
                    @click="impersonate(company)"
                >
                    <v-list-tile-avatar>
                        <v-avatar size="35">
                            <v-img :src="company.photo" />
                        </v-avatar>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title class="body-2">
                            {{ company.name }}
                        </v-list-tile-title>

                        <v-list-tile-sub-title class="text-capitalize">
                            {{ $t('labels.organization') }}
                            - ({{ company.user_employment.role }})
                        </v-list-tile-sub-title>
                    </v-list-tile-content>

                    <v-list-tile-action>
                        <v-icon
                            :color="isImpersonated(company) ? 'green' : 'grey'"
                            small
                        >
                            circle
                        </v-icon>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </v-card>

        <v-divider />

        <v-btn
            type="submit"
            color="primary"
            flat
            @click="signoutAllAccounts"
        >
            <v-icon
                left
                small
                >logout</v-icon
            >
            {{ $t('buttons.signout-all-account') }}
        </v-btn>
    </v-layout>
</template>

<script>
import LocaleSwitcher from './LocaleSwitcher.vue'
import HandlesImpersonations from '../Mixins/HandlesImpersonations'

export default {
    components: {
        LocaleSwitcher,
    },

    mixins: [HandlesImpersonations],

    props: {
        user: {
            type: Object,
            required: true,
        },
        locale: {
            type: String,
        },
        locales: {
            type: Object,
        },
        companies: {
            type: Array,
            required: true,
        },
    },

    methods: {
        impersonate(company) {
            if (this.isImpersonated(company)) {
                this.impersonateLeave()
            } else {
                this.impersonateEnter(company)
            }
        },

        signoutAllAccounts() {
            this.$inertia.delete(this.$route('auth.logout'))
        },
    },
}
</script>
