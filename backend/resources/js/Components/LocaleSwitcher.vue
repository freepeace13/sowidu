<template>
    <form
        ref="form"
        method="POST"
        action="/change-language"
    >
        <input
            type="hidden"
            name="_token"
            :value="csrfToken"
        />
        <input
            type="hidden"
            name="locale"
            :value="locale"
        />

        <v-menu offset-y>
            <template #activator="{ on }">
                <v-btn
                    class="mx-auto my-0"
                    :block="block"
                    flat
                    v-on="on"
                >
                    {{ languages[locale] }} <v-icon>arrow_drop_down</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-tile
                    v-for="(value, key) in languages"
                    :key="key"
                    @click="switchLocale(key)"
                >
                    <v-list-tile-content>
                        <v-list-tile-title
                            :class="{ 'blue--text': key === lang }"
                        >
                            {{ value }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-menu>
    </form>
</template>

<script>
export default {
    props: {
        lang: {
            type: String,
            default: null,
        },

        languages: {
            type: Object,
            default: () => ({}),
        },

        block: {
            type: Boolean,
            default: false,
        },
    },

    data: (vm) => ({
        locale: vm.lang,
    }),

    computed: {
        csrfToken() {
            return document.querySelector('meta[name="csrf-token"').content
        },
    },

    methods: {
        switchLocale(locale) {
            this.locale = locale
            this.$nextTick(() => this.$refs.form.submit())
        },
    },
}
</script>
