<template>
    <v-layout row wrap>
        <v-flex xs3 class="pr-1">
            <HouseNumberSelector
                v-model="formHouse"
                :errors="[]"
                label="House No."
            />
        </v-flex>
        <v-flex xs9 class="pl-1">
            <StreetSelector
                v-model="formStreet"
                :errors="[]"
                label="Street name"
            />
        </v-flex>
        <v-flex xs12>
            <CountrySelector
                v-model="formCountry"
                :errors="[]"
                label="Country"
            />

            <StateSelector
                :errors="[]"
                v-model="formState"
                :country="formCountry"
                label="State"
            />

            <v-layout row>
                <v-flex xs6 class="pr-1">
                    <CitySelector
                        :state="formState"
                        :country="formCountry"
                        v-model="formCity"
                        :errors="[]"
                        label="City"
                    />
                </v-flex>
                <v-flex xs6 class="pl-1">
                    <ZipcodeSelector
                        v-model="formZipcode"
                        :errors="[]"
                        label="Zipcode"
                    />
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
const formProp = (name) => ({
    get: function () {
        return Reflect.get(this, name);
    },
    set: function (value) {
        this.$emit(`update:${name}`, value);
    }
});

export default {
    name: 'AddressFormWidget',

    props: ['house', 'street', 'country', 'state', 'city', 'zipcode'],

    computed: {
        formHouse: formProp('house'),
        formStreet: formProp('street'),
        formCountry: formProp('country'),
        formState: formProp('state'),
        formCity: formProp('city'),
        formZipcode: formProp('zipcode'),
    }
}
</script>
