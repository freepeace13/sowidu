<template>
    <VBtn
        :color="color"
        :disabled="isProcessing"
        :loading="isProcessing"
        v-bind="$attrs"
        @click="$emit('click')"
    >
        <slot />

        <template #loader>
            <span> {{ $t('buttons.saving') }}... </span>
        </template>
    </VBtn>
</template>

<script>
export default {
    props: {
        isProcessing: {
            type: Boolean,
            default: false,
        },
        color: {
            required: false,
            type: String,
            default: 'primary',
        },
    },
}
</script>
<style scoped>
.v-btn[disabled] {
    background-color: rgba(0, 0, 0, 0.12) !important;
    box-shadow: none !important;
    pointer-events: none;
}
</style>
