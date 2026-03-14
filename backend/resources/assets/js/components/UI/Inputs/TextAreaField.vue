<template>
    <div :class="[{ 'plain-text': plaintext }, 'textarea-field']">
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-textarea
            :label="null"
            v-bind="{...$attrs, ...$props}"
            :readonly="readonly"
            v-on="$listeners"
            :error="Array.from(new Set(errors)).length > 0"
            :error-messages="errors"
            @input="e => $emit('input', e)"
            @change="e => $emit('change', e)"
            solo
        ></v-textarea>
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

            placeholder: {
                type: String
            },

            errors: {
                type: Array,
                default: () => ([])
            },

            readonly: {
                type: Boolean,
                default: false
            },

            plaintext: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style lang="scss" scoped>
    .textarea-field {
        width: 100%;

        /deep/ .v-text-field__details {
            padding: 0px !important;
        }

        &.plain-text {
            /deep/ .v-text-field .v-input__slot {
                background: #EEEEEE !important;
                box-shadow: none !important;
                input {
                    color: #000;
                }
            }
        }
    }
</style>
