<template>
    <form @submit.prevent="onSubmitHandler">
            <v-layout flex align-center justify-center>
                <v-flex >
                    <v-card class="regForm gradient-45deg-black-grey">
                        <h2 class="text-md-center loginTitle">
                            Company <span class="grey--text loginSubTitle">Registration</span>
                        </h2>

                        <v-alert :value="error_message" type="error">
                            {{ error_message ? error_message : '' }}
                        </v-alert>

                        <v-container grid-list-md text-xs-center>
                            <v-layout row wrap mt-3>
                                <v-flex xs5 offset-xs1>
                                    <v-text-field
                                        label="Name of Institution"
                                        ref="name"
                                        v-model="form.name"
                                        :error-messages="errors.name"
                                        :error="errors.name !== undefined"
                                    >
                                    </v-text-field>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap mt-3>
                                <v-flex xs5 offset-xs1>
                                    <LegalFormSelector v-model="form.legal_form"/>
                                    <InstitutionTypeSelector v-model="form.institution_type"/>
                                    <SpecializationSelector v-model="form.specialization"/>
                                </v-flex>

                                <v-flex xs4 offset-xs1>
                                    <v-layout row wrap class="mt--10">
                                        <v-flex xs6>

                                            <CountrySelector
                                                v-model="form.country"
                                                :errors="errors.country"/>

                                            <StateSelector
                                                v-model="form.state"
                                                :country="form.country"
                                                :errors="errors.state"/>
                                        </v-flex>

                                        <v-flex xs6>
                                            <CitySelector
                                                v-model="form.city"
                                                :country="form.country"
                                                :state="form.state"
                                                :errors="errors.city"/>

                                            <ZipcodeSelector
                                                v-model="form.zipcode"
                                                :errors="errors.zipcode"/>
                                        </v-flex>

                                        <v-flex xs6>
                                            <StreetSelector
                                                v-model="form.street"
                                                :errors="errors.street"/>

                                            <HouseNumberSelector
                                                v-model="form.house_no"
                                                :errors="errors.house_no"/>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                            </v-layout>
                        </v-container>

                        <v-layout row wrap class="mt-10">
                            <v-flex xs10 offset-xs1>
                                <v-checkbox
                                    v-model="form.terms_accepted"
                                    :error-messages="errors.terms_accepted"
                                    :error="errors.terms_accepted !== undefined"
                                    label="I agree to Trade of Terms. In order to proceed please confirm."
                                >
                                </v-checkbox>
                            </v-flex>
                        </v-layout>

                        <v-layout row wrap mt-3>
                            <v-flex xs6 offset-xs3>
                                <v-btn
                                    small
                                    class="gradient-45deg-light-blue-cyan"
                                    type="submit"
                                    style="background: linear-gradient(45deg, #0288d1 0%, #26c6da 100%)"
                                    :loading="sending_request"
                                >
                                    Register Company
                                </v-btn>
                                <v-btn small flat>Cancel and return to Desktop</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card>
                </v-flex>
            </v-layout>
    </form>
</template>

<script>
    import axios from 'axios'

    export default {
        data: () => ({
            form: {
                name: null,
                legal_form: "",
                institution_type: "",
                specialization: "",
                street: null,
                house_no: null,
                zipcode: null,
                city: null,
                state: null,
                country: null,
                terms_accepted: false,
            },
            errors: [],
            error_message: null,
            sending_request: false,
            show: false
        }),

        methods: {
            async onSubmitHandler () {
                try {
                    this.errors = []
                    this.error_message = null
                    this.sending_request = true

                    const { data } = await axios.post('/api/companies', { ...this.form })

                    this.sending_request = false
                    this.$store.dispatch('auth/fetchCompanies')
                    this.$events.$emit('alert', 'You successfully created new company.')
                    this.$router.push({ name: 'desktop' })
                } catch (e) {
                    this.sending_request = false
                    this.errors = e.errors || {}
                    this.error_message = e.message
                }
            }
        }
    }
</script>
