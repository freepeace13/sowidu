<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">Create an account</h2>

        <v-card width="80%" class="mx-auto">
            <v-card-text class="pa-5">
                <form @submit.prevent="register">
                    <v-layout row>
                        <v-flex xs6 class="pr-1">
                            <TextField
                                v-model="form.firstName"
                                :errors="$auth.$errors.get('first_name', [])"
                                prepend-inner-icon="person"
                                label="Firstname"
                            ></TextField>
                        </v-flex>
                        <v-flex xs6 class="pl-1">
                            <TextField
                                :errors="$auth.$errors.get('last_name', [])"
                                v-model="form.lastName"
                                label="Lastname"
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
                        label="Password"
                    ></TextField>

                    <TextField
                        v-model="form.passwordConfirmation"
                        prepend-inner-icon="vpn_key"
                        type="password"
                        label="Confirm Password"
                    ></TextField>

                    <v-checkbox
                        :error="$auth.$errors.has('agreement')"
                        v-model="form.agreement"
                        class="ma-0"
                        label="I hereby agree to the terms and conditions."
                    ></v-checkbox>

                    <v-btn
                        type="submit"
                        :loading="$auth.$loading"
                        color="primary"
                        large block
                        :disabled="!form.agreement"
                    >
                        Proceed
                    </v-btn>

                    <router-link
                        :to="{ name: 'auth.login' }"
                        class="text-xs-center d-block mt-3"
                    >
                        BACK TO LOGIN
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
    }),

    computed: {
        oppositeVouchIcon() {
            return this.vouch === 'email' ? 'phone' : 'email'
        }
    },

    methods: {
        async register() {
            const vToken = await this.$auth.register({
                ...this.form,
                [this.vouch]: this[this.vouch]
            });

            this.$router.replace({
                name: 'middleforms.verifications',
                params: { token: vToken },
                query: {
                    redirect: '/account/confirmation'
                }
            });
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
