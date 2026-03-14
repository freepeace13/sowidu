<template>
    <main>
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Billing Address</h1>
        <h2 class="text-xs-center mb-3">One more thing, we need your billing address</h2>

        <v-card width="80%" class="mx-auto">
            <v-card-text class="pa-5">
                <form @submit.prevent="onSubmit">
                    <v-layout row>
                        <v-flex xs3 class="pr-1">
                            <HouseNumberSelector
                                label="House No."
                                v-model="form.house_number"
                                :errors="errors.house_number">
                            </HouseNumberSelector>
                        </v-flex>
                        <v-flex xs9 class="pl-1">
                            <StreetSelector
                                label="Street name"
                                v-model="form.street"
                                :errors="errors.street">
                            </StreetSelector>
                        </v-flex>
                    </v-layout>

                    <CountrySelector
                        label="Country"
                        v-model="form.country"
                        :errors="errors.country">
                    </CountrySelector>

                    <StateSelector
                        label="State"
                        v-model="form.state"
                        :country="form.country"
                        :errors="errors.state">
                    </StateSelector>

                    <v-layout row>
                        <v-flex xs6 class="pr-1">
                            <CitySelector
                                label="City"
                                v-model="form.city"
                                :country="form.country"
                                :state="form.state"
                                :errors="errors.city">
                            </CitySelector>
                        </v-flex>
                        <v-flex xs6 class="pl-1">
                            <ZipcodeSelector
                                label="Zipcode"
                                v-model="form.zipcode"
                                :errors="errors.zipcode">
                            </ZipcodeSelector>
                        </v-flex>
                    </v-layout>

                    <v-btn
                        type="submit"
                        color="primary"
                        large
                        block
                        :loading="loading"
                        :disabled="loading"
                    >
                        Proceed
                    </v-btn>

                    <div class="text-xs-center">
                        <v-btn
                            flat
                            @click="skipNow"
                            :disabled="loading"
                        >
                            Skip For Now
                        </v-btn>
                    </div>
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
            form: {
                street: null,
                city: null,
                house_number: null,
                state: null,
                zipcode: null,
                country: null,
            },
            errors: {},
            loading: false
        }),

        methods: {
            async skipNow() {
                try {
                    this.loading = true

                    const { data } = await axios.patch(`/api/address/skip`)

                    this.$store.dispatch('auth/setAccount', data.data)
                    this.$router.push({ name: 'desktop' })
                } finally {
                    this.loading = false
                }
            },

            async onSubmit() {
                try {
                    this.loading = true

                    const { data } = await axios.post('/api/address', this.form)

                    this.$store.dispatch('auth/changeAccountAddress', data.data)
                    this.$router.push({ name: 'desktop' })
                } catch (e) {
                    this.errors = e.errors || {}
                } finally {
                    this.loading = false
                }
            }
        },

        beforeRouteEnter(to, from, next) {
            next(vm => {
                if (!vm.$store.getters['auth/redirectToAddress']) {
                    vm.$router.replace({ name: 'desktop' })
                }
            })
        }
    }
</script>
