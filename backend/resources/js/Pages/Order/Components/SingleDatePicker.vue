<script setup>
import { ref } from 'vue'
import { useDateFormat } from '@/Composables/useDayJs'

const props = defineProps({
    modelValue: {
        required: false,
        type: String,
    },
    placeholder: {
        required: false,
        type: String,
    },
    isLoading: {
        required: false,
        type: Boolean,
        default: false,
    },
    isClearable: {
        required: false,
        type: Boolean,
        default: true,
    },
})

defineEmits(['update:modelValue'])

const date = ref(props.modelValue)

function getPickerDates(date) {
    if (!date) return date
    return useDateFormat(date, 'MMM DD, YYYY', null)
}
</script>
<template>
    <v-menu
        :close-on-content-click="false"
        lazy
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
    >
        <template #activator="{ on }">
            <v-text-field
                :value="getPickerDates(date)"
                :placeholder="placeholder"
                color="primary"
                solo
                hide-details
                flat
                :loading="isLoading"
                :disabled="isLoading"
                readonly
                :clearable="isClearable"
                v-on="on"
                @click:clear="() => $emit('update:modelValue', null)"
            />
        </template>
        <v-date-picker
            v-model="date"
            color="primary"
            header-color="green"
            @input="(val) => $emit('update:modelValue', val)"
        />
    </v-menu>
</template>
