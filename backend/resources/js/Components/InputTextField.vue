<template>
  <v-text-field
    :value="value"
    v-bind="{ singleLine, placeholder, ...$attrs }"
    color="blue-grey darken-2"
    :placeholder="singleLine ? placeholder : ' '"
    full-width
    outline
    v-on="inputListeners"
  >
    <template v-if="label" #label>
      <input-label :label="label" :required="$attrs.required" />
    </template>
  </v-text-field>
</template>

<script>
import InputLabel from './InputLabel.vue';

export default {

    components: { InputLabel },
    inheritAttrs: false,

    props: {
        label: String,
        value: [String, Number],
        placeholder: String,
        singleLine: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        inputListeners() {
            const vm = this;

            return Object.assign({},
                this.$listeners,
                {
                    input(event) {
                        vm.$emit('input', event);
                    },
                },
            );
        },
    },
};
</script>
