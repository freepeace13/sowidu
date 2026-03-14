<template>
    <v-textarea
        :value="value"
        v-bind="$attrs"
        v-on="inputListeners"
        color="blue-grey darken-2"
        placeholder=" "
        auto-grow
        outline
    >
        <template #label v-if="label">
            <input-label :label="label" :required="$attrs.required" />
        </template>
    </v-textarea>
</template>

<script>
    import InputLabel from './InputLabel.vue';

    export default {
        inheritAttrs: false,

        props: ['label', 'value'],

        components: { InputLabel },

        computed: {
            inputListeners() {
                const vm = this;

                return Object.assign({},
                    this.$listeners,
                    {
                        input(event) {
                            vm.$emit('input', event);
                        }
                    }
                )
            }
        }
    }
</script>
