<template>
    <div :class="[{ 'plain-text': plaintext }, 'text-field']">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-text-field
            :label="null"
            v-bind="{...$attrs, ...$props}"
            :error="Array.from(new Set(errors)).length > 0"
            v-on="$listeners"
            :error-messages="errors"
            @input="e => $emit('input', e)"
            @change="e => $emit('change', e)"
            solo
        ></v-text-field>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                validator(v) {
                    return typeof v === 'string' || v === null || typeof v === 'number'
                }
            },

            label: {
                type: String
            },

            errors: {
                type: Array,
                default: () => ([])
            },

            plaintext: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style lang="scss" scoped>
    .text-field {
        width: 100%;

        /deep/ .v-text-field__details {
            padding: 0px !important;
        }

        // &.plain-text {
        //     /deep/ .v-text-field .v-input__slot {
        //         background: #EEEEEE !important;
        //         box-shadow: none !important;
        //         input {
        //             color: #000;
        //         }
        //     }
        // }
    }
</style>
