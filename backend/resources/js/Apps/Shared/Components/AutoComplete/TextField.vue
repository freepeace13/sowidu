<template>
    <v-menu offset-y>
        <template #activator="{ on }">
            <v-text-field
                v-bind="{ ...$attrs }"
                v-on="{ ...on, ...$listeners }"
            />
        </template>

        <v-list v-show="items.length > 0">
            <v-list-tile
                v-for="item in items"
                :key="item"
                @click="$emit('update:input', item)"
            >
                <v-list-tile-title>{{ item }}</v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    name: 'TextAutoCompleteField',

    props: {
        items: {
            type: Array,
            default: () => [],
        },
    },
}
</script>
