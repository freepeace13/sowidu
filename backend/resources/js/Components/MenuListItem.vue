<template>
    <v-list-tile
        tag="div"
        v-bind="$attrs"
        v-on="$listeners"
    >
        <v-list-tile-avatar v-if="icon">
            <v-icon>{{ icon }}</v-icon>
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>
                <slot />
            </v-list-tile-title>
        </v-list-tile-content>
    </v-list-tile>
</template>
<script>
export default {
    props: {
        icon: {
            type: String,
            default: null,
        },
    },
}
</script>
