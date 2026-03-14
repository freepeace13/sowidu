<script setup>
defineProps({
    label: {
        required: true,
        type: String,
    },
    value: {
        required: true,
        type: String,
    },
    isBold: {
        required: false,
        type: Boolean,
        default: true,
    },
})
</script>
<template>
    <tr class="tw-text-center">
        <td
            colspan="4"
            class="tw-text-right"
        >
            <div :class="{ 'tw-font-semibold': isBold }">
                {{ label }}
            </div>
        </td>
        <td class="tw-whitespace-nowrap tw-font-semibold">
            {{ value }}
        </td>
    </tr>
</template>
