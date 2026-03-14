<template>
    <div class="lockscreen">
        <v-container fluid fill-height>
            <v-layout row wrap>
                <v-flex xs4 offset-xs4 align-self-center>
                    <form class="validation-form" @submit.prevent="onSubmit">
                        <div class="account-info">
                            <v-avatar size="100">
                                <img :src="account.avatar"/>
                            </v-avatar>

                            <div class="account-name">
                                {{ account.fullName }}
                            </div>
                        </div>

                        <v-text-field
                            v-model="password"
                            type="password"
                            placeholder="Password"
                            solo
                            dark
                            :error="error !== null"
                            :error-messages="error"
                            append-icon="lock_open"
                            :loading="loading"
                            @click:append="onSubmit"/>
                    </form>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'
    import axios from 'axios'

    export default {
        data: () => ({
            password: null,
            loading: false,
            error: null
        }),

        computed: {
            ...mapGetters('auth/user', ['account', 'token'])
        },

        methods: {
            async onSubmit() {
                try {
                    this.loading = true
                    this.error = null

                    await axios.post('/api/password/unlock', { password: this.password }, {
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    })

                    this.$lockscreen.close()
                    window.location.reload()
                } catch (e) {
                    this.loading = false
                    this.error = e.message
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .lockscreen {
        background: #424242;
        height: 100vh;

        .validation-form {
            .account-info {
                width: 50%;
                text-align: center;
                margin: 0 auto;
                margin-bottom: 15px;

                .account-name {
                    font-size: 20px;
                    letter-spacing: 1px;
                    line-height: 50px;
                    color: #ffff;
                }
            }
        }
    }
</style>
