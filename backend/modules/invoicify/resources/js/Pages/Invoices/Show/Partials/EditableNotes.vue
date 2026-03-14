<script setup>
import { useForm, usePage } from '@inertiajs/vue2'
import { get } from '@vueuse/core'
import { computed, ref } from 'vue'
import SubmitButton from '~Shared/Components/SubmitButton.vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const { invoice } = usePage().props
const { $t } = useGlobalVariables()

const form = useForm({
    notes: invoice.value.notes,
})

const isFocusedOnNotes = ref(false)

const invoiceNotesWasChanged = computed(() => invoice.value.notes != form.notes)

function updateInvoiceNotes() {
    form.patch(
        window.route('invoices.update', {
            invoice: get(invoice, 'id'),
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['flash', 'invoice'],
        },
    )
}
</script>

<template>
    <v-flex xs12>
        <v-textarea
            v-model="form.notes"
            outline
            height="200"
            :disabled="form.processing || !invoice.can_be_edited"
            :loading="form.processing"
            :placeholder="$t('invoices.form.add-notes')"
            :error-messages="form.errors.notes"
            :hide-details="!form.errors.notes"
            @focusin="() => invoice.can_be_edited && (isFocusedOnNotes = true)"
            @focusout="
                () => invoice.can_be_edited && (isFocusedOnNotes = false)
            "
        />
        <div class="tw-text-right">
            <SubmitButton
                v-show="isFocusedOnNotes || invoiceNotesWasChanged"
                :is-processing="form.processing"
                class="mx-0"
                @click="updateInvoiceNotes"
            >
                {{ $t('buttons.save-changes') }}
            </SubmitButton>
        </div>
    </v-flex>
</template>
