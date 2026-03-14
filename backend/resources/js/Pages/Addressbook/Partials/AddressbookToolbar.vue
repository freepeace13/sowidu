<template>
    <v-toolbar
        absolute
        top
        flat
        color="white"
    >
        <v-toolbar-title v-if="$vuetify.breakpoint.smAndUp">
            {{ currentPage?.title }}
        </v-toolbar-title>
        <v-spacer />
        <v-toolbar-items
            :class="{
                'w-full': $vuetify.breakpoint.xs,
            }"
        >
            <v-text-field
                :placeholder="`${$t('labels.search')}...`"
                single-line
                flat
                outline
                prepend-inner-icon="search"
                hide-details
                full-width
                :loading="isLoading"
                :class="[
                    'dense !tw-items-center',
                    {
                        small: $vuetify.breakpoint.smAndDown,
                    },
                ]"
                :value="search"
                @input="(e) => $emit('search', e)"
            />
        </v-toolbar-items>
    </v-toolbar>
</template>
<script>
export default {
    props: {
        pages: {
            type: Array,
            required: true,
        },
        isLoading: {
            required: false,
            type: Boolean,
            default: false,
        },
        initialSearch: {
            required: false,
            type: String,
            default: null,
        },
    },

    data: () => ({
        search: null,
    }),

    computed: {
        currentPage() {
            return this.pages.find(({ route, title }) => {
                return (
                    this.$route(route, null, false) === this.$page.url ||
                    this.$route().current(route) ||
                    this.$route().current(
                        `addressbooks.${title.toLowerCase()}.*`,
                    )
                )
            })
        },
    },

    mounted() {
        if (this.initialSearch) this.search = this.initialSearch
    },
}
</script>
