<script setup>
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const { $confirm } = useGlobalVariables()

const emit = defineEmits(['confirm'])

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    question: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'info',
    },
})

function handlePress() {
    $confirm({
        title: props.title,
        question: props.question,
        type: props.type,
        confirm: () => emit('confirm'),
    })
}
</script>

<template>
    <span>
        <slot
            v-bind="{
                on: { click: handlePress },
                attrs: {
                    disabled: false,
                },
            }"
        />
    </span>
</template>
