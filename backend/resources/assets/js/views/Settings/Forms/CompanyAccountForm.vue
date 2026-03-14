<template>
    <v-card color="grey darken-4">
        <form @submit.prevent="submit" class="pa-5">
            <v-layout row>
                <v-flex xs8>
                    <TextField
                        v-model="form.name"
                        label="Name"
                        :errors="errors.name">
                    </TextField>

                    <v-layout row>
                        <v-flex xs6>
                            <LegalFormSelector
                                v-model="form.legal_form"
                                label="Legal Form">
                            </LegalFormSelector>
                        </v-flex>
                        <v-flex xs6>
                            <InstitutionTypeSelector
                                v-model="form.institution_type"
                                label="Institution Type">
                            </InstitutionTypeSelector>
                        </v-flex>
                    </v-layout>
                </v-flex>
                <v-flex xs4 class="text-xs-center">
                    <v-avatar
                      size="130"
                      class="grey darken-4"
                     >
                        <img src="https://i.pinimg.com/originals/6d/04/4b/6d044b7318cf19556a437a8c3eb8e82b.png">
                    </v-avatar>

                    <v-btn color="primary" class="mt-3">Choose Avatar</v-btn>
                </v-flex>
            </v-layout>

            <v-divider></v-divider>
            <v-subheader class="px-0">Default address</v-subheader>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco</p>

            <v-layout row>
                <v-flex xs10>
                    <v-card>
                        <v-card-text>
                            {{ form.address ? form.address.long_address : 'No default address yet.' }}
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs2 align-self-center class="text-xs-right">
                    <a href="#" @click="viewAddresses">Change</a>
                </v-flex>
            </v-layout>

            <v-layout row class="mt-5">
                <v-flex>
                    <v-btn :loading="loading" block color="primary" type="submit">
                        Update
                    </v-btn>
                </v-flex>
                <v-flex>
                    <v-btn block color="grey darken-3">
                        Cancel
                    </v-btn>
                </v-flex>
            </v-layout>
        </form>
    </v-card>
</template>

<script>
    import axios from 'axios'

    export default {
        data: () => ({
            form: {
                first_name: null,
                last_name: null,
                avatar: null,
                address: null,
                legal_form: null,
                institution_type: null,
            },
            errors: {},
            loading: false
        }),

        methods: {
            async submit() {
                try {
                    this.loading = true
                    this.errors = {}
                    const { data } = await axios.patch(`/api/settings/account/company`, this.form)
                    this.form = { ...data.data }
                    this.$store.dispatch('auth/company/setCompany', data.data)
                    this.$events.$emit('alert', 'Account settings changes updated')
                } catch (e) {
                    console.log(e)
                    this.errors = e.errors || {}
                } finally {
                    this.loading = false
                }
            },

            viewAddresses() {
                this.$modal.show({
                    size: 'md',
                    title: 'Change Address',
                    modal: require('~/components/UI/Modals/AddressSelector').default,
                    listeners: {
                        select: (address) => {
                            this.$store.dispatch('auth/changeAccountAddress', address)
                            this.form.address = address
                        }
                    }
                })
            }
        },

        created() {
            this.form = {
                ...this.$store.getters['auth/activeAccount']
            }
        }
    }
</script>

<style lang="scss" scoped>
    /deep/ .v-input__control {
        width: 100% !important;

        .v-radio {
            margin-right: 0px;
        }

        .v-input--selection-controls__input {
            margin-right: 15px;
        }

        .v-label {
            width: 100%;
        }
    }
</style>
