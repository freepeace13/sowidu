<script setup>
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useDateFormat } from '@/Composables/useDayJs'

defineExpose({ show })

const isShow = ref(false)
const invoice = ref(null)

const form = useForm({
    payment_date: null,
})

const { $route } = useGlobalVariables()

function show(model) {
    if (!model) {
        return
    }

    isShow.value = true
    invoice.value = model

    form.payment_date = useDateFormat(model.payment_date, 'YYYY-MM-DD')
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
        $route('invoices.update', {
            invoice: invoice.value,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['invoice'],
            onSuccess: () => {
                close()
            },
        },
    )
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.labels.update-payment-date') }}
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
                            <v-menu
                                :close-on-content-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.payment_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.payment_date
                                        "
                                        :hide-details="
                                            !form.errors.payment_date
                                        "
                                        :label="
                                            $t('invoices.labels.payment-date')
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        class="required-input"
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.payment_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
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
