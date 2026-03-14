<template>
    <ModalLayout v-bind="$attrs" :id="$attrs.modal.id">
        <h1 class="text-xs-center mb-1 display-3 font-weight-bold">Billing Address</h1>
        <h2 class="text-xs-center mb-3">One more thing, we need your billing address</h2>

        <v-card class="mx-auto" style="width:30%;">
            <v-card-text class="pa-5">
                <form @submit.prevent="create">
                    <v-layout row>
                        <v-flex xs3 class="pr-1">
                            <HouseNumberSelector
                                label="House No."
                                v-model="address.$refs.houseNumber"
                                :errors="errors.get('house_number', [])">
                            </HouseNumberSelector>
                        </v-flex>
                        <v-flex xs9 class="pl-1">
                            <StreetSelector
                                label="Street name"
                                v-model="address.$refs.street"
                                :errors="errors.get('street', [])">
                            </StreetSelector>
                        </v-flex>
                    </v-layout>

                    <CountrySelector
                        label="Country"
                        v-model="address.$refs.country"
                        :errors="errors.get('country', [])">
                    </CountrySelector>

                    <StateSelector
                        label="State"
                        v-model="address.$refs.state"
                        :country="address.$refs.country"
                        :errors="errors.get('state', [])">
                    </StateSelector>

                    <v-layout row>
                        <v-flex xs6 class="pr-1">
                            <CitySelector
                                label="City"
                                v-model="address.$refs.city"
                                :country="address.$refs.country"
                                :state="address.$refs.state"
                                :errors="errors.get('city', [])">
                            </CitySelector>
                        </v-flex>
                        <v-flex xs6 class="pl-1">
                            <ZipcodeSelector
                                label="Zipcode"
                                v-model="address.$refs.zipcode"
                                :errors="errors.get('zipcode', [])">
                            </ZipcodeSelector>
                        </v-flex>
                    </v-layout>

                    <v-btn
                        type="submit"
                        color="primary"
                        large block
                        :loading="$create.$loading"
                        :disabled="$skip.$loading || $create.$loading"
                    >
                        Proceed
                    </v-btn>

                    <div class="text-xs-center">
                        <v-btn
                            flat
                            @click="skip"
                            :disabled="$skip.$loading || $create.$loading"
                        >
                            Skip For Now
                        </v-btn>
                    </div>
                </form>
            </v-card-text>
        </v-card>

        <div class="text-xs-center mt-3">
            &copy; 2020 Sowidu. All rights reserved.
        </div>
    </ModalLayout>
</template>

<script>
import { createResource } from 'vue-async-manager';
import { Address } from '~/services/models';
import { MessageBag } from '~/support/wrappers';

export default {
    name: 'CreateFirstAddress',

    data: () => ({
        address: Address.create({
            street: null,
            city: null,
            houseNumber: null,
            state: null,
            zipcode: null,
            country: null,
        }),
        errors: new MessageBag
    }),

    methods: {
        async skip() {
            await this.$skip.read();
            this.$modal.close(this.$vnode.key);
        },

        async create() {
            try {
                await this.$create.read(this.address);
                this.$modal.close(this.$vnode.key);
            } catch (error) {
                this.errors = error;
            }
        }
    },
    
    created() {
        const { dispatch } = this.$store;

        this.$skip = createResource(() => dispatch('address/skip'));
        this.$create = createResource((address) => dispatch('address/create', address));
    }
}
</script>
