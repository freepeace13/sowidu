<template>
    <v-layout
        row
        wrap
    >
        <v-flex
            sm6
            xs12
        >
            <HouseNumberField
                v-model="address.house_number"
                :label="$t('labels.inputs.house-no')"
                outline
                color="primary"
                :disabled="isLoading"
                :error-messages="errors['address.house_number']"
                :hide-details="!errors['address.house_number']"
                :loading="isLoading"
                :readonly="readonly"
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <StreetField
                v-model="address.street"
                :loading="isLoading"
                :disabled="isLoading"
                :readonly="readonly"
                :error-messages="errors['address.street']"
                :label="$t('labels.inputs.street')"
                :hide-details="!errors['address.street']"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <ZipcodeField
                v-model="address.zipcode"
                :label="$t('labels.inputs.zipcode')"
                outline
                color="primary"
                :disabled="isLoading"
                :error-messages="errors['address.zipcode']"
                :hide-details="!errors['address.zipcode']"
                :loading="isLoading"
                :readonly="readonly"
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <CityField
                v-model="address.city"
                :country-code="address.country?.code ?? $t('labels.all')"
                :loading="isLoading"
                :readonly="readonly"
                :hide-details="!hasErrorOn('city')"
                :error-messages="hasErrorOn('city')"
                browser-autocomplete="new-city"
                :placeholder="$t('select-or-search-for-city')"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <StateField
                v-model="address.state"
                :country-code="address.country?.code ?? $t('labels.all')"
                :loading="isLoading"
                :readonly="readonly"
                :hide-details="!hasErrorOn('state')"
                :error-messages="hasErrorOn('state')"
                browser-autocomplete="new-state"
                :placeholder="$t('hints.select-or-search-for-state')"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <CountryField
                v-model="address.country"
                :label="$t('labels.inputs.country')"
                :loading="isLoading"
                :readonly="readonly"
                :hide-details="!hasErrorOn('country')"
                :error-messages="hasErrorOn('country')"
                browser-autocomplete="new-country"
                :placeholder="$t('hints.start-typing-to-search-country')"
                color="primary"
                outline
                class="required-input"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import CityField from './CityField.vue'
import CountryField from './CountryField.vue'
import HouseNumberField from './HouseNumberField.vue'
import StateField from './StateField.vue'
import StreetField from './StreetField.vue'
import ZipcodeField from './ZipcodeField.vue'

export default {
    components: {
        ZipcodeField,
        HouseNumberField,
        StreetField,
        CityField,
        StateField,
        CountryField,
    },
    props: {
        value: {
            type: Object,
            required: true,
        },
        isLoading: {
            required: false,
            type: Boolean,
            default: false,
        },
        errors: {
            required: false,
            type: Object,
            default: () => ({}),
        },
        readonly: {
            required: false,
            type: Boolean,
            default: false,
        },
        errorKey: {
            required: false,
            type: String,
            default: 'address',
        },
    },
    computed: {
        address() {
            return this.value
        },
    },

    methods: {
        hasErrorOn(field) {
            return (
                this.errors[`${this.errorKey}.${field}`] || this.errors[field]
            )
        },
    },
}
</script>
