<script setup>
import { useForm } from '@inertiajs/vue2'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { watch } from 'vue'

const { $route } = useGlobalVariables()
const form = useForm({
    amount: null,
    name: null,
    operator: null,
    taxable: false,
})

const props = defineProps({
    invoice: Object,
    close: Function,
    permissions: Object,
    activeTab: {
        type: String,
        required: true,
    },
})

watch(
    () => props.activeTab,
    (activeTab) => {
        if (activeTab === 'invoice_deduction') {
            form.reset()
        }
    },
)

const submit = () => {
    form.post($route('invoices.deduct.manual', { invoice: props.invoice.id }), {
        preserveScroll: true,
        only: ['invoiceSummary'],
        onSuccess: () => {
            form.reset()
            props.close()
        },
    })
}
</script>

<template>
    <div class="tw-mt-10">
        <v-text-field
            v-model="form.name"
            :loading="form.processing"
            :disabled="form.processing"
            :error-messages="form.errors.name"
            :hide-details="!form.errors.name"
            label="Name"
            required
            color="primary"
            outline
            class="required-input"
        />
        <div class="tw-flex tw-mt-5">
            <v-select
                v-model="form.operator"
                :loading="form.processing"
                :disabled="form.processing"
                :error-messages="form.errors.operator"
                :hide-details="!form.errors.operator"
                label="Operator"
                :items="['-', '%']"
                required
                color="primary"
                outline
                class="required-input mr-2 tw-w-[10px]"
            />
            <v-text-field
                v-model="form.amount"
                :loading="form.processing"
                :disabled="form.processing"
                :error-messages="form.errors.amount"
                :hide-details="!form.errors.amount"
                label="Amount"
                required
                color="primary"
                outline
                class="required-input tw-flex-1 ml-2"
            />
        </div>
        <v-card-actions class="!tw-px-0 tw-mt-3.5">
            <v-spacer />
            <v-btn
                :disabled="form.processing"
                outline
                depressed
                @click="close"
            >
                {{ $t('buttons.close') }}
            </v-btn>
            <SubmitButton
                :loading="form.processing"
                :disabled="form.processing"
                @click="submit"
            >
                {{ $t('invoices.buttons.add-to-invoice') }}
            </SubmitButton>
        </v-card-actions>
    </div>
</template>
