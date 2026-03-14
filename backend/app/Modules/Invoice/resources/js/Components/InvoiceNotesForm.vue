<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ show })

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
})
const emit = defineEmits(['refresh'])

const isShow = ref(false)
const form = useForm({
    notes: null,
})

const { $route } = useGlobalVariables()

function show() {
    isShow.value = true

    form.notes = props.invoice.notes
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    form.reset()
    form.clearErrors()
}

function submit() {
    const invoice = props.invoice

    form.patch(
        $route('invoices.update.subject_and_description', {
            invoice,
        }),
        {
            only: ['invoice', 'items'],
            onSuccess: () => {
                close()
                emit('refresh')
            },
        },
    )
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        width="90%"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.preview.update-notes') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.notes"
                                outline
                                :disabled="form.processing"
                                :loading="form.processing"
                                :label="$t('invoices.form.notes')"
                                :error-messages="form.errors.notes"
                                :hide-details="!form.errors.notes"
                                height="400"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4 py-3">
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="() => submit()"
                >
                    {{ $t('buttons.update') }}
                    <template #loader>
                        <span>
                            {{ $t('buttons.updating') }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
