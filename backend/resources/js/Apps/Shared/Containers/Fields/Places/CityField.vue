<template>
    <CityProvider :country="country">
        <template #default="{ items }">
            <v-select
                class="required-input"
                v-bind="{ ...$attrs, items }"
                v-on="$listeners"
            />
        </template>
    </CityProvider>
</template>

<script>
import CityProvider from '../../Providers/CityProvider'

export default {
    name: 'CityField',

    components: {
        CityProvider,
    },

    props: {
        country: {
            type: String,
            default: null,
        },
    },
}
</script>
