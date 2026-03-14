<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { ref } from 'vue'
import AddressAutocomplete from '@/Components/AddressAutocomplete.vue'

defineExpose({ show })
const emit = defineEmits(['refresh'])

const isShow = ref(false)
const invoice = ref(null)

const form = useForm({
    construction_site: null,
    execution_period_start: null,
    execution_period_end: null,
})

const { $route } = useGlobalVariables()

function show(model) {
    if (!model) {
        return
    }

    isShow.value = true
    invoice.value = model
    form.construction_site = model.construction_site
    form.execution_period_start = model.execution_period?.start
    form.execution_period_end = model.execution_period?.end
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
            only: ['invoice'],
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
                    {{ $t('invoices.labels.edit') }}
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
                            <AddressAutocomplete
                                v-model="form.construction_site"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.construction-site')"
                                :error-messages="form.errors.construction_site"
                                :hide-details="!form.errors.construction_site"
                            />
                        </v-flex>
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
                                        :value="form.execution_period_start"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.execution_period_start
                                        "
                                        :hide-details="
                                            !form.errors.execution_period_start
                                        "
                                        :label="
                                            $t(
                                                'invoices.form.execution-period-start',
                                            )
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
                                    v-model="form.execution_period_start"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
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
                                        :value="form.execution_period_end"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.execution_period_end
                                        "
                                        :hide-details="
                                            !form.errors.execution_period_end
                                        "
                                        :label="
                                            $t(
                                                'invoices.form.execution-period-end',
                                            )
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
                                    v-model="form.execution_period_end"
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
