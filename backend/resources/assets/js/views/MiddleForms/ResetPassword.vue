<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Sowidu</h1>
        <h2 class="text-xs-center mb-3">You can now set your new password</h2>

        <v-card width="80%" class="mx-auto">
            <v-card-text class="pa-5">
                <form @submit.prevent="reset">
                    <v-alert type="error" class="mb-3" :value="message !== null">
                        {{ message }}
                    </v-alert>

                    <TextField
                        v-model="password"
                        type="password"
                        :errors="errors.password"
                        label="New Password"
                    ></TextField>

                    <TextField
                        v-model="password_confirmation"
                        type="password"
                        label="Confirm New Password"
                    ></TextField>

                    <v-btn type="submit" color="primary" large block>Proceed</v-btn>
                </form>
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
            message: null,
            errors: {},
            password: null,
            password_confirmation: null
        }),

        methods: {
            async checkToken(token) {
                try {
                    await axios.get(`/api/verifications/${token}/verified`)
                } catch {
                    this.$page.abort(404)
                }
            },

            async reset() {
                try {
                    const { token, callback } = this.$route.query

                    const { data } = await axios.patch(`/api/password/reset/${token}`, {
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    })

                    if (! callback) {
                        this.$router.replace({ name: 'auth.login' })
                    } else {
                        window.location.href = `${callback}?token=${token}`
                    }
                } catch(e) {
                    this.errors = e.errors || {}
                    if (e.status === 403) this.message = e.message || null
                }
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => vm.checkToken(to.query.token))
        },

        async beforeRouteUpdate(to, from, next) {
            await this.checkToken(to.query.token)
            next()
        }
    }
</script>
