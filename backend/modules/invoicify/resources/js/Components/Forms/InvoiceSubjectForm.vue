<script setup>
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import SubmitButton from '~Shared/Components/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ show })

defineProps({
    width: {
        type: [String, Number],
        default: '90%',
    },
})

const emit = defineEmits(['refresh'])

const isShow = ref(false)
const invoice = ref(null)

const form = useForm({
    subject: null,
    description: null,
})

const { $route } = useGlobalVariables()

function show(model) {
    if (!model) {
        return
    }

    isShow.value = true
    invoice.value = model

    form.subject = model.subject
    form.description = model.description
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    invoice.value = null
    form.reset()
    form.clearErrors()
}

function submit() {
    form.patch(
        $route('invoices.update.subject_and_description', {
            invoice: invoice.value,
        }),
        {
            only: ['invoice'],
            onSuccess: () => {
                close()
                emit('refresh')
            },
            onError: console.error,
        },
    )
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        :width="width"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.preview.update-subject-message') }}
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
                            <v-text-field
                                v-model="form.subject"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.subject"
                                :hide-details="!form.errors.subject"
                                :label="$t('invoices.preview.subject')"
                                required
                                color="primary"
                                outline
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.description"
                                outline
                                :disabled="form.processing"
                                :loading="form.processing"
                                :label="$t('invoices.preview.message')"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                height="200"
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
