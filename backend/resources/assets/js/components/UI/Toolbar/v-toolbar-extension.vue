<template>
    <v-toolbar height="64" card>
        <div class="subheading font-weight-bold h-100">
            {{ title }}
        </div>

        <slot></slot>
    </v-toolbar>
</template>

<script>
export default {
    name: 'v-toolbar-extension',

    props: {
        title: String
    }
}
</script>