<template>
    <v-avatar
        tile
        :size="$vuetify.breakpoint.xs ? '26' : avatarSize"
        class="mr-2"
        :color="color"
    >
        <v-icon
            v-if="isIcon"
            dark
            :size="iconSize"
        >
            {{ src }}
        </v-icon>
        <v-img
            v-else
            :size="avatarSize"
            class="border-primary"
            :src="src"
            :alt="title"
        />
    </v-avatar>
</template>
<script>
export default {
    props: {
        src: {
            type: String,
            default: '/storage/board-logo.png',
        },

        title: {
            type: String,
            default: null,
        },

        isIcon: {
            type: Boolean,
            default: false,
        },

        color: {
            type: String,
            default: 'primary',
        },

        iconSize: {
            type: [String, Number],
            default: 16,
        },

        avatarSize: {
            type: Number,
            default: 30,
        },
    },
}
</script>
