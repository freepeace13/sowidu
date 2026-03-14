<template>
    <v-btn
        v-bind="$attrs"
        v-on="$listeners"
        @click="$refs.input.click()"
    >
        <input
            type="file"
            ref="input"
            :multiple="multiple"
            @change="onUpload"
        />

        <slot></slot>
    </v-btn>
</template>

<script>
export default {
    props: {
        multiple: {
            type: Boolean,
            default: false
        }
    },

    methods: {
        onUpload(event) {
            this.$emit('upload', event);
            this.$refs.input.value = null;
        }
    }
}
</script>

<style scoped>
    input[type=file] {
        display: none;
    }
</style>