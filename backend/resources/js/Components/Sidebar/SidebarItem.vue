<template>
    <v-list-tile
        full-width
        :px-0="$vuetify.breakpoint.xs"
        :justify-center="$vuetify.breakpoint.xs"
        :value="isActive"
        active-class="grey lighten-4"
        @click="$inertia.visit(url, { preserveState: true })"
    >
        <v-list-tile-avatar>
            <v-icon>
                {{ icon }}
            </v-icon>
        </v-list-tile-avatar>
        <v-list-tile-content v-if="$vuetify.breakpoint.smAndUp">
            <v-badge
                v-if="badge > 0"
                right
                color="red"
            >
                <template #badge>
                    <span v-text="badge" />
                </template>
                {{ label }}
            </v-badge>
            <span v-else>{{ label }}</span>
        </v-list-tile-content>
    </v-list-tile>
</template>
<script>
export default {
    props: {
        label: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
        isActive: {
            type: Boolean,
            required: false,
            default: false,
        },
        url: {
            type: String,
            required: true,
        },
        badge: {
            required: false,
            type: Number,
            default: 0,
        },
    },
}
</script>
