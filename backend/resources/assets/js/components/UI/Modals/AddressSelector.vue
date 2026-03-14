<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-tabs
            v-model="tabs"
            centered
            color="grey darken-4"
            slider-color="grey"
            grow
        >
            <v-tab href="#tab-choose">Choose Address</v-tab>
            <v-tab href="#tab-create-new">Create New Address</v-tab>
        </v-tabs>
        <v-divider></v-divider>

        <v-container grid-list-lg fluid>
            <v-tabs-items v-model="tabs">
                <v-tab-item value="tab-choose">
                    <v-list dense v-for="address in addresses" :key="address.id" class="mb-2">
                        <v-list-tile avatar @click="changeActiveAddress(address)">
                            <v-list-tile-content>
                                <v-list-tile-title>{{ address.long_address }}</v-list-tile-title>
                            </v-list-tile-content>
                            <v-list-tile-avatar v-if="address.is_active">
                                <v-icon color="success">check</v-icon>
                            </v-list-tile-avatar>
                        </v-list-tile>
                    </v-list>
                </v-tab-item>
                <v-tab-item value="tab-create-new">
                    <v-layout row>
                        <v-flex xs3 class="pr-1">
                            <HouseNumberSelector
                                v-model="form.house_number"
                                :errors="errors.house_number"
                                label="House No.">
                            </HouseNumberSelector>
                        </v-flex>
                        <v-flex xs9 class="pl-1">
                            <StreetSelector
                                v-model="form.street"
                                :errors="errors.street"
                                label="Street name">
                            </StreetSelector>
                        </v-flex>
                    </v-layout>

                    <CountrySelector
                        :errors="errors.country"
                        v-model="form.country"
                        label="Country">
                    </CountrySelector>

                    <v-layout row>
                        <v-flex xs6 class="pr-1">
                            <CitySelector
                                :state="form.state"
                                :country="form.country"
                                :errors="errors.city"
                                v-model="form.city"
                                label="City">
                            </CitySelector>
                        </v-flex>
                        <v-flex xs6 class="pl-1">
                            <ZipcodeSelector
                                :errors="errors.zipcode"
                                v-model="form.zipcode"
                                label="Zipcode">
                            </ZipcodeSelector>
                        </v-flex>
                    </v-layout>

                    <StateSelector
                        :country="form.country"
                        :errors="errors.country"
                        v-model="form.state"
                        label="State">
                    </StateSelector>
                </v-tab-item>
            </v-tabs-items>
        </v-container>

        <template v-slot:actions>
            <v-btn
                :loading="loading"
                color="primary"
                @click="createNewAddress"
                block
            >
                Create Address
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">Cancel</v-btn>
        </template>
    </ModalLayout>
</template>

<script>
    import axios from 'axios'

    export default {
        data: () => ({
            tabs: 'tab-choose',
            addresses: [],
            form: {
                street: null,
                city: null,
                house_number: null,
                state: null,
                zipcode: null,
                country: null,
            },
            loading: false,
            errors: {}
        }),

        methods: {
            async fetchAddresses() {
                const { data } = await axios.get(`/api/address`)
                this.addresses = data.data
            },

            async createNewAddress() {
                try {
                    this.loading = true
                    const { data } = await axios.post('/api/address', this.form)
                    this.loading = false
                    this.addresses = this.addresses.map(e => ({ ...e, is_active: false }))
                    this.addresses.splice(0, 0, data.data)
                    this.tabs = 'tab-choose'
                } catch (e) {
                    this.loading = false
                    this.errors = e.errors || {}
                }
            },

            async changeActiveAddress({ id }) {
                const { data } = await axios.patch(`/api/address/${id}/set-as-active`)
                this.$emit('select', data.data)
                this.$modal.close(this.$vnode.key)
            }
        },

        created() {
            this.fetchAddresses()
        }
    }
</script>
