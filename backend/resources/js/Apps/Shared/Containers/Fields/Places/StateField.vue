<template>
    <StateProvider :country="country">
        <template #default="{ items }">
            <v-select
                class="required-input"
                v-bind="{ ...$attrs, items }"
                v-on="$listeners"
            />
        </template>
    </StateProvider>
</template>

<script>
import StateProvider from '../../Providers/StateProvider'

export default {
    name: 'StateField',

    components: {
        StateProvider,
    },

    props: {
        country: {
            type: String,
            default: null,
        },
    },
}
</script>
