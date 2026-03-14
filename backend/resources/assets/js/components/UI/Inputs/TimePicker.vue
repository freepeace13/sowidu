<template>
    <v-menu
        ref="menu"
        :close-on-content-click="false"
        transition="slide-x-transition"
        :disabled="readonly"
        full-width
        offset-y
        offset-overflow
        nudge-right="15"
        nudge-top="25"
        max-width="400px"
        format="24hr"
    >
        <TextField
            :label="label"
            slot="activator"
            :value="value"
            :errors="errors"
            readonly
            :plaintext="plaintext"
        ></TextField>

        <v-time-picker
            header-color="grey darken-4"
            full-width
            :value="value"
            :readonly="readonly"
            :min="min"
            :max="max"
            @change="e => $emit('input', e)"
        ></v-time-picker>
    </v-menu>
</template>

/**
 * Time picker compoennt
 *
 * @props {string} min - (default: '') Set minimum allowed date. Form should be 'YYYY-MM-DD' ex: '2012-03-23'
 * @props {string} max - (default: '') Set maxmum allowed date. Form should be 'YYYY-MM-DD' ex: '2012-03-23'
 * @type {Object}
 */
<script>
    export default {
        props: {
            value: {
                validator(v) {
                    return typeof v === 'string' || v === null
                }
            },

            errors: {
                type: Array,
                default: () => ([])
            },

            label: {
                type: String
            },

            readonly: {
                type: Boolean,
                default: false
            },

            plaintext: {
                type: Boolean,
                default: false
            },

            min: {
                type: String,
                required: false,
                default: ''
            },

            max: {
                type: String,
                required: false,
                default: ''
            }
        }
    }
</script>
