<template>
    <v-toolbar :height="height" app fixed>
        <slot name="side-icon"></slot>

        <v-toolbar-title
            class="body-2 font-weight-bold mx-3 text-uppercase"
            v-html="title"
        />

        <v-spacer></v-spacer>

        <v-toolbar-items class="align-center" style="flex:auto;">
            <v-text-field
                solo
                hide-details
                single-line
                flat
                class="v-toolbar-search-field"
                height="45"
                :placeholder="$t('labels.search')"
                append-icon="search"
            />
        </v-toolbar-items>

        <v-spacer class="hidden-xs-only"></v-spacer>

        <slot name="additional-items"></slot>

        <template v-slot:extension>
            <slot name="extension"></slot>
        </template>
    </v-toolbar>
</template>

<script>
export default {
    name: 'ApplicationHeader',

    props: {
        height: {
            type: [String, Number],
            default: 64
        },

        title: {
            type: String,
            default: "Desktop"
        }
    }
}
</script>

<style lang="scss" scoped>
    @import './styles.scss';
</style>
