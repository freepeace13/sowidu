<template>
    <v-toolbar flat>
        <v-toolbar-title v-if="title">{{ title }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items class="hidden-sm-and-down">
            <slot name="actions"></slot>
        </v-toolbar-items>
    </v-toolbar>
</template>

<script>
export default {
    name: 'EditOrderActionsToolbar',

    props: {
        title: {
            type: String,
            default: null
        }
    }
}
</script>