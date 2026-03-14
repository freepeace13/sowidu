<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">First, we need your {{ vouchAttributeName.toLowerCase() }}</h2>

        <v-card class="mx-auto">
            <v-card-text class="pa-5">
                <WebForm url="/auth/forgot-password" method="post">
                    <template v-slot="{ errors }">
                        <TextField
                            name="recipient"
                            :errors="errors.get('recipient')"
                            v-model="$data[vouch]"
                            :prepend-inner-icon="vouch"
                            @click:prepend-inner="vouch = oppositeVouchIcon"
                            :mask="vouch !== 'email' ? '###-####-#####' : null"
                            :label="vouchAttributeName"
                        ></TextField>

                        <div class="mb-3">
                            By entering your {{ vouchAttributeName.toLowerCase() }} will help us find your account details and verify accordingly
                            by sending verification code.
                        </div>

                        <v-divider class="my-3"></v-divider>

                        <v-btn type="submit" color="primary" large block>
                            {{ $t('buttons.proceed') }}
                        </v-btn>
                        <v-btn large block v-router-push="{ name: 'auth.login' }">
                            {{ $t('buttons.back-to-login') }}
                        </v-btn>
                    </template>
                </WebForm>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">&copy; 2020 Sowidu. All rights reserved.</div>
    </main>
</template>

<script>
import WebForm from '~/components/containers/WebForm';
import axios from 'axios'

export default {
    layout: 'Guest',

    components: {
        WebForm
    },

    data: () => ({
        email: null,
        phone: null,
        vouch: 'email',
    }),

    computed: {
        oppositeVouchIcon() {
            return this.vouch === 'email' ? 'phone' : 'email'
        },

        vouchAttributeName() {
            return this.vouch !== 'email' ? this.$t('labels.inputs.mobile') : this.$t('labels.inputs.email')
        }
    },

    methods: {
        async submit() {
            try {
                const { data } = await axios.post(`/api/password/forgot`, {
                    [this.vouch]: this.$data[this.vouch]
                })

                this.$router.replace({
                    name: 'middleforms.verifications',
                    params: { token: data.token },
                    query: {
                        redirect: this.$router.resolve({
                            name: 'middleforms.resetpassword'
                        }).href
                    }
                })
            } catch (e) {
                this.errors = e.errors || {}
            }
        }
    }
}
</script>
