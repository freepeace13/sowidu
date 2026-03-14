<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">
            {{ $t('phrase.login.please-login-into-your-account') }}
        </h2>

        <v-card class="mx-auto">
            <v-card-text class="pa-5">
                <v-alert
                    icon="warning"
                    class="mb-4"
                    color="red"
                    :value="$auth.$errors.message"
                >
                    Invalid username/password.
                </v-alert>

                <form @submit.prevent="authenticate">
                    <TextField
                        :errors="$auth.$errors.get('username')"
                        v-model="credentials.username"
                        :prepend-inner-icon="vouch"
                        @click:prepend-inner="vouch = oppositeVouchIcon"
                        :mask="vouch !== 'person' ? '(###) #### - #####' : null"
                        :label="vouch !== 'person' ? vouch : $t('labels.inputs.email-username')"
                        :hint="$t('hints.inputs.email-username-or-phone')"
                    ></TextField>

                    <TextField
                        v-model="credentials.password"
                        type="password"
                        :errors="$auth.$errors.get('password')"
                        :label="$t('labels.inputs.password')"
                        prepend-inner-icon="vpn_key"
                    ></TextField>

                    <v-btn
                        type="submit"
                        color="primary"
                        :loading="$auth.$loading"
                        large block
                    >
                        {{ $t('buttons.login') }}
                    </v-btn>

                    <router-link :to="{ name: 'auth.password.forgot' }" class="d-block text-xs-center">
                        {{ $t('questions.auth.forgot-password') }}
                    </router-link>

                    <v-divider class="my-4"></v-divider>
                </form>

                <v-btn large block v-router-push="{ name: 'auth.register' }">
                    {{ $t('buttons.create-account') }}
                </v-btn>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">&copy; 2020 Sowidu. All rights reserved.</div>
    </main>
</template>

<script>
import config from '~/config';
import HandlesAuthentications from '~/components/Mixins/HandlesAuthentications';

export default {
    layout: 'Guest',

    mixins: [HandlesAuthentications()],

    data: () => ({
        credentials: {
            username: null,
            password: null,
        },
        vouch: 'person',
    }),

    computed: {
        oppositeVouchIcon() {
            return this.vouch === 'person' ? 'phone' : 'person'
        },
    },

    methods: {
        async authenticate() {
            await this.$auth.authenticate({
                credentials: this.credentials,
                guard: this.$guards.user,
            });

            this.$router.push(config('auth.redirectUrl'));
        }
    }
}
</script>
