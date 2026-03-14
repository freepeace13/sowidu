<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">{{ $t('headings.create-an-account') }}</h2>

        <v-card class="mx-auto">
            <v-card-text class="pa-5">
                <form @submit.prevent="register">
                    <v-alert :value="registered" color="success">
                        {{ $t('alerts.auth.successful-registration') }}
                    </v-alert>

                    <v-layout row>
                        <v-flex xs6 class="pr-1">
                            <TextField
                                v-model="form.firstName"
                                :errors="$auth.$errors.get('first_name', [])"
                                prepend-inner-icon="person"
                                :label="$t('labels.inputs.firstname')"
                            ></TextField>
                        </v-flex>
                        <v-flex xs6 class="pl-1">
                            <TextField
                                :errors="$auth.$errors.get('last_name', [])"
                                v-model="form.lastName"
                                :label="$t('labels.inputs.lastname')"
                            ></TextField>
                        </v-flex>
                    </v-layout>

                    <TextField
                        :errors="$auth.$errors.get(vouch, [])"
                        v-model="$data[vouch]"
                        :prepend-inner-icon="vouch"
                        @click:prepend-inner="vouch = oppositeVouchIcon"
                        :mask="vouch !== 'email' ? '(###) #### - #####' : null"
                        :label="vouch"
                    ></TextField>

                    <TextField
                        :errors="$auth.$errors.get('password', [])"
                        v-model="form.password"
                        prepend-inner-icon="vpn_key"
                        type="password"
                        :label="$t('labels.inputs.password')"
                    ></TextField>

                    <TextField
                        v-model="form.passwordConfirmation"
                        prepend-inner-icon="vpn_key"
                        type="password"
                        :label="$t('labels.inputs.confirm-password')"
                    ></TextField>

                    <v-checkbox
                        :error="$auth.$errors.has('agreement')"
                        v-model="form.agreement"
                        class="ma-0"
                        :label="$t('hints.inputs.terms-agreement')"
                    ></v-checkbox>

                    <v-btn
                        type="submit"
                        :loading="$auth.$loading"
                        color="primary"
                        large block
                        :disabled="!form.agreement"
                    >
                        {{ $t('buttons.proceed') }}
                    </v-btn>

                    <router-link
                        :to="{ name: 'auth.login' }"
                        class="text-xs-center d-block mt-3"
                    >
                        {{ $t('buttons.back-to-login') }}
                    </router-link>
                </form>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">
            &copy; 2020 Sowidu. All rights reserved.
        </div>
    </main>
</template>

<script>
import axios from 'axios'
import { MessageBag } from '~/support/wrappers';
import AuthService from '~/services/AuthService';
import { createContext } from '~/support/factories';

export default {
    layout: 'Guest',

    data: () => ({
        form: {
            firstName: null,
            lastName: null,
            password: null,
            passwordConfirmation: null,
            agreement: false,
        },
        vouch: 'email',
        phone: null,
        email: null,
        registered: false
    }),

    computed: {
        oppositeVouchIcon() {
            return this.vouch === 'email' ? 'phone' : 'email'
        }
    },

    methods: {
        async register() {
            await this.$auth.register({
                ...this.form,
                [this.vouch]: this[this.vouch]
            });

            this.registered = true;
        }
    },

    created() {
        this.$auth = createContext({
            async register(payload) {
                return await AuthService.register(payload);
            }
        })
    }
}
</script>
