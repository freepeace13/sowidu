<template>
    <v-card flat color="transparent">
        <GroupSettingsWrapper
            title="Address settings"
            subtitle="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Possimus impedit commodi quis animi libero asperiores iure, quasi odio adipisci sunt distinctio et veritatis corporis itaque quo quam molestias blanditiis dolorem?"
        >
            <v-layout column>
                <template v-if="defaultAddress">
                    <v-flex>Current address</v-flex>
                    <v-flex>
                        <v-card>
                            <v-card-text>{{ defaultAddress.label }}</v-card-text>
                        </v-card>                        
                    </v-flex>
                </template>
                <v-flex>Addresses</v-flex>
                <v-flex>
                    <v-expansion-panel v-for="(address, index) in addresses" :key="address.id" expand>
                        <v-expansion-panel-content>
                            <template v-slot:header>
                                <div class="subtitle">{{ address.label }}</div>
                            </template>

                            <v-card>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <AddressFormWidget
                                        :house.sync="addresses[index].$refs.houseNumber"
                                        :street.sync="addresses[index].$refs.street"
                                        :country.sync="addresses[index].$refs.country"
                                        :state.sync="addresses[index].$refs.state"
                                        :city.sync="addresses[index].$refs.city"
                                        :zipcode.sync="addresses[index].$refs.zipcode"
                                    />

                                    <v-switch v-model="addresses[index].isActive" label="Set as default" />
                                </v-card-text>
                                <v-card-actions class="justify-end">
                                    <v-btn color="primary" @click="updateAddress(addresses[index])">Save</v-btn>
                                    <v-btn flat color="red">Delete Address</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-flex>
            </v-layout>
        </GroupSettingsWrapper>
        <GroupSettingsWrapper
            title="Create new address"
            subtitle="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Possimus impedit commodi quis animi libero asperiores iure, quasi odio adipisci sunt distinctio et veritatis corporis itaque quo quam molestias blanditiis dolorem?"
        >
            <v-card flat>
                <v-card-text>
                    <AddressFormWidget
                        :house.sync="newAddress.$refs.houseNumber"
                        :street.sync="newAddress.$refs.street"
                        :country.sync="newAddress.$refs.country"
                        :state.sync="newAddress.$refs.state"
                        :city.sync="newAddress.$refs.city"
                        :zipcode.sync="newAddress.$refs.zipcode"
                    />
                    <v-switch v-model="newAddress.isActive" label="Set as default address" />
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions class="justify-end">
                    <v-btn color="primary" @click="createAddress(newAddress)">
                        Create Address
                    </v-btn>
                </v-card-actions>
            </v-card>
        </GroupSettingsWrapper>
    </v-card>
</template>

<script>
import apiCalls from '~/services/AddressService';
import Address from '~/services/models/address';
import { createResource } from 'vue-async-manager';
import GroupSettingsWrapper from '../components/GroupSettingsWrapper';
import AddressFormWidget from '../widgets/AddressForm';

export default {
    name: 'Address',

    components: {
        GroupSettingsWrapper,
        AddressFormWidget
    },

    data: () => ({
        addresses: [],
        newAddress: new Address({
            isActive: false,
            street: null,
            houseNumber: null,
            zipcode: null,
            city: null,
            state: null,
            country: null,
            reference: {
                streetId: null,
                houseNumberId: null,
                zipcodeId: null,
                cityId: null,
                stateId: null,
                countryId: null,
            }
        })
    }),

    computed: {
        defaultAddress() {
            return this.addresses.find((address) => address.isActive);
        }
    },

    methods: {
        deactivateAddresses() {
            this.addresses = this.addresses.map((address) => Object.assign(address, {
                isActive: false
            }));
        },

        updateAddress(address) {
            if (! address.isActive) {
                return this.$events.$emit('alert', 'Update address not support at this time.');
            }

            apiCalls.activate(address.id)
                .then((result) => {
                    this.deactivateAddresses();

                    const existing = this.addresses.findIndex((address) => address.equals(result));
                    
                    if (existing !== -1) {
                        this.addresses.splice(existing, 1, result);
                    }

                    this.$events.$emit('alert', 'Current address updated.');
                })
                .catch((error) => {
                    this.$events.$emit('alert', 'Error while trying to update current address.');
                    console.error('Update current address error: ', error);
                })
        },

        createAddress(address) {
            apiCalls.create(address)
                .then((result) => {
                    if (result.isActive) {
                        this.deactivateAddresses();
                    }

                    this.addresses.push(result);
                    this.newAddress = Address.create();

                    this.$events.$emit('alert', 'New address has been created.')
                })
                .catch((error) => {
                    this.$events.$emit('alert', `Error while trying to create a new address.`)
                    console.error('create address error: ', error);
                });
        }
    },

    created() {
        this.$rm = createResource(async () => {
            this.addresses = await apiCalls.all();
        });

        this.$watch((vm) => [
            vm.$store.getters['auth/token']('user'),
            vm.$store.getters['auth/token']('company')
        ], (value) => {
            this.$rm.read();
        }, { immediate: true });
    }
}
</script>
