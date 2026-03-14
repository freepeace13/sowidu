<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ showForm })

const { $route } = useGlobalVariables()
const props = defineProps({
    start: {
        type: String,
        required: true,
    },
    end: {
        type: String,
        required: true,
    },
    invoice: {
        type: Number,
        required: true,
    },
})

const durationForm = ref(null)
const form = useForm({
    start: props.start,
    end: props.end,
})
const show = ref(false)

const submit = () => {
    form.patch($route('invoice.duration.update', { invoice: props.invoice }), {
        onSuccess: () => {
            show.value = false
        },
    })
}

function showForm() {
    show.value = true
    console.log('🚀 ~ showForm ~ show:', show)
}
</script>

<template>
    <div
        ref="durationForm"
        class="tw-relative no-print"
    >
        <!-- <button
            class="no-print"
            @click.prevent="show = !show"
        /> -->
        <div
            v-show="show"
            class="tw-absolute tw-top-full tw-right-0 tw-border tw-shadow tw-rounded tw-pt-4 tw-z-[1000] tw-px-4 tw-bg-white"
        >
            <div class="tw-flex tw-items-start mb-2">
                <input
                    v-model="form.start"
                    type="date"
                    :max="form.end"
                    class="w-full border tw-p-2 tw-mr-1.5 tw-text-sm"
                />
                <input
                    v-model="form.end"
                    type="date"
                    :min="form.start"
                    class="w-full border tw-p-2 tw-ml-1.5 tw-text-sm"
                />
            </div>
            <div class="tw-flex tw-justify-end tw-py-2">
                <v-btn
                    color="primary"
                    depressed
                    class="!tw-m-0"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Update
                </v-btn>
            </div>
        </div>
    </div>
</template>
