<template functional>
    <v-menu offset-y>
        <template #activator="{ on }">
            <v-text-field
                v-bind="data.attrs"
                v-on="on"
                @input="props.onTextChanged"
            />
        </template>

        <v-list v-show="props.items.length > 0">
            <v-list-tile
                v-for="item in props.items"
                :key="item"
                @click="props.onTextChanged(item)"
            >
                <v-list-tile-title>{{ item }}</v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    functional: true,

    props: {
        items: {
            type: Array,
            default: () => [],
        },

        onTextChanged: {
            type: Function,
            required: true,
        },
    },
}
</script>
