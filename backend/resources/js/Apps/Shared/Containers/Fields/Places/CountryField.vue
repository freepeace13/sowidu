<template>
    <CountryProvider>
        <template #default="{ items }">
            <v-select
                class="required-input"
                v-bind="{ ...$attrs, items }"
                v-on="$listeners"
            />
        </template>
    </CountryProvider>
</template>

<script>
import CountryProvider from '../../Providers/CountryProvider'

export default {
    name: 'CountryField',

    components: {
        CountryProvider,
    },
}
</script>
