<template>
    <v-text-field
        solo
        hide-details
        flat
        class="v-toolbar-search-field"
        :height="height"
        placeholder="Search"
        append-icon="search"
    />
</template>

<script>
export default {
    name: 'v-toolbar-search-form',

    props: {
        width: {
            type: String,
            default: 'auto'
        },

        height: {
            type: String,
            default: '40'
        }
    }
}
</script>

<style lang="scss" scoped>
.v-toolbar-search-field {
    /deep/.v-input__slot {
        min-height: initial;
    }

    &.collapsed {
        display: none;
    }

    &.collapsed.activated {
        display: block;
        width: 100%;
    }
}
</style>