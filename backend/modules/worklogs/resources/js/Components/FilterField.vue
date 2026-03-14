<script setup>
defineProps({
    label: {
        required: true,
        type: String,
    },
})
</script>
<template>
    <div class="tw-grid md:tw-grid-cols-7 tw-grid-cols-2 tw-gap-x-2 pb-4">
        <div
            class="tw-col-span-2 md:tw-col-span-1 info-label white--text tw-flex tw-items-center tw-justify-start tw-text-left md:mr-2"
        >
            {{ label }}
        </div>

        <slot />
    </div>
</template>
