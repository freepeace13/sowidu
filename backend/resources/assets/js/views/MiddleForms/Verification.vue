<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">Enter the verification code</h2>

        <v-card width="80%" class="mx-auto">
            <v-card-text class="pa-5">
                <form @submit.prevent="verify">
                    <v-alert :type="!isExpired ? `success` : `error`" :value="true" class="mb-3">
                        <span v-if="isExpired">Verification token is already expired.</span>
                        <span v-else>We sent an verification code to your email address or phone number.</span>
                    </v-alert>

                    <TextField
                        v-model="code"
                        :errors="errors.code"
                        placeholder="Enter 6-digit code here"
                        mask="NNNNNN"
                        label="verification code"
                    ></TextField>

                    <v-btn type="submit" color="primary" large block>Continue</v-btn>
                    <v-divider class="my-3"></v-divider>
                </form>

                <p>If you have not receive any email within 5 minutes you can resend a new code.</p>

                <v-btn large block @click="resend" :loading="resending">Resend New Code</v-btn>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">&copy; 2020 Sowidu. All rights reserved.</div>
    </main>
</template>

<script>
    import axios from 'axios'

    export default {
        layout: 'Guest',

        data: () => ({
            isExpired: false,
            code: null,
            errors: {},
            resending: false,
            verifying: false,
        }),

        methods: {
            async checkToken(token) {
                try {
                    await axios.get(`/api/verifications/${token}`)
                    this.isExpired = false
                } catch(e) {
                    if (e.status === 400) this.isExpired = true
                    if (e.status === 403) this.$page.abort(404)
                }
            },

            async verify() {
                try {
                    const { token } = this.$route.params
                    const { redirect } = this.$route.query

                    this.verifying = true

                    const { data } = await axios.patch(`/api/verifications/${token}/verify`, {
                        code: this.code
                    })

                    this.verifying = false
                    window.location.href = (redirect || '/') + `?token=${token}`
                } catch (e) {
                    this.verifying = false

                    if (e.status === 422) this.errors = e.errors || {}
                    if (e.status === 400) this.isExpired = true
                    if (e.status === 403) this.$page.abort(404)
                }
            },

            async resend() {
                try {
                    const { token } = this.$route.params
                    const { redirect } = this.$route.query

                    this.resending = true

                    const { data } = await axios.post(`/api/verifications/${token}`)

                    this.resending = false
                    this.$router.replace({
                        name: 'middleforms.verifications',
                        params: { token: data.token },
                        query: {
                            redirect: (redirect || '/') + `?token=${data.token}`
                        }
                    })
                } catch (e) {
                    this.resending = false
                    console.error('Resending error', e)
                }
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.checkToken(to.params.token))
        },

        async beforeRouteUpdate(to, from, next) {
            await this.checkToken(to.params.token)
            next()
        }
    }
</script>
