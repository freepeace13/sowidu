<template>
    <v-menu top>
        <v-btn slot="activator" depressed>
            {{ locales[value] }}
        </v-btn>

        <v-card>
            <v-list>
                <v-list-tile
                    v-for="[locale, name] of Object.entries(locales)" :key="locale"
                    @click="selectLocale(locale)"
                >
                    <v-list-tile-content>
                        {{ name }}
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    name: 'LocaleSelector',

    props: {
        value: {
            type: String,
            required: true,
            validator(prop) {
                return ['de', 'en'].indexOf(prop) !== -1;
            }
        }
    },

    computed: {
        locales() {
            return this.shared('translation.locales');
        }
    },

    methods: {
        selectLocale(locale) {
            this.$emit('input', locale);
            this.$emit('change', locale);
        }
    }
}
</script>
