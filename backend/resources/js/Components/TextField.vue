<template>
    <v-text-field v-bind="inheritProps" single-line box v-on="$listeners">
        <template v-slot:prepend-inner>
            <label class="caption mr-2">
                {{ label }} <span v-show="required" class="red--text">*</span>
            </label>
        </template>
    </v-text-field>
</template>

<script>
    export default {
        props: {
            required: {
                type: Boolean,
                default: false
            },

            label: {
                type: String,
                required: true
            }
        },

        computed: {
            inheritProps() {
                const { singlLine, outline, solo, label,
                        reverse, ...inheritProps } = this.$attrs;

                return inheritProps;
            }
        }
    }
</script>
