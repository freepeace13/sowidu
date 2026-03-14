<template>
    <v-app>
        <v-toolbar
            color="primary"
            dark
            flat
            app
        >
            <v-toolbar-title>{{ title }}</v-toolbar-title>
        </v-toolbar>

        <slot />
    </v-app>
</template>

<script>
export default {
    props: {
        title: {
            required: true,
            type: String,
        },
    },
}
</script>
