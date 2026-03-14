<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'

defineExpose({ show })
const emit = defineEmits(['refresh'])

const isShow = ref(false)
const offer = ref(null)

const form = useForm({
    subject: null,
    message: null,
    notes: null,
})

const { $route } = useGlobalVariables()

function show(model) {
    if (!model) {
        return
    }

    isShow.value = true
    offer.value = model

    form.subject = model.subject
    form.message = model.message
    form.notes = model.notes
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    offer.value = null
    form.reset()
    form.clearErrors()
}

function submit() {
    form.patch(
        $route('offers.messages.update', {
            offer: offer.value.id,
        }),
        {
            only: ['offer'],
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
                    {{ $t('offer.labels.edit-subject-message-notes') }}
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
                                :label="$t('offer.labels.subject')"
                                required
                                color="primary"
                                outline
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.message"
                                outline
                                :disabled="form.processing"
                                :loading="form.processing"
                                :label="$t('offer.labels.message')"
                                :error-messages="form.errors.message"
                                :hide-details="!form.errors.message"
                                height="200"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <v-textarea
                                v-model="form.notes"
                                outline
                                :disabled="form.processing"
                                :loading="form.processing"
                                :label="$t('offer.labels.notes')"
                                :error-messages="form.errors.notes"
                                :hide-details="!form.errors.notes"
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
