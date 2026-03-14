<script setup>
import { router, useForm, Link as InertiaLink } from '@inertiajs/vue2'
import { computed, ref } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'

defineExpose({
    show,
})

const { $route } = useGlobalVariables()
const isShow = ref(false)
const invoice = ref(null)
const form = useForm({
    tax: null,
})

const addableTaxes = computed(() => getPageProps('addableTaxes', []))

function show(invoiceData) {
    if (!invoiceData) return

    invoice.value = invoiceData
    isShow.value = true

    router.reload({
        only: ['addableTaxes'],
    })
}

function close() {
    form.reset()
    invoice.value = null
    isShow.value = false
}

function submit() {
    form.post(
        $route('invoices.taxes.store', {
            invoice: invoice.value,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            only: ['invoice', 'invoiceSummary'],
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
                    {{ $t('invoices.labels.add-tax') }}
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
                        <v-flex
                            v-show="!addableTaxes.length"
                            xs12
                        >
                            <v-alert
                                type="info"
                                :value="true"
                            >
                                {{
                                    $t(
                                        'invoices.message.tax.empty-addable-taxes',
                                    )
                                }}
                                <InertiaLink
                                    :href="$route('account.settings.tax.index')"
                                    class="white--text tw-font-semibold"
                                >
                                    {{ $t('buttons.create-new-one') }} here...
                                </InertiaLink>
                            </v-alert>
                        </v-flex>
                        <v-flex
                            v-show="addableTaxes.length"
                            xs12
                        >
                            <v-radio-group
                                v-model="form.tax"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.tax"
                                :hide-details="!form.errors.tax"
                                required
                                color="primary"
                                outline
                                class="mt-0 pt-0"
                            >
                                <template #label>
                                    <div>
                                        {{ $t('invoices.tax.form.select-tax') }}
                                    </div>
                                </template>
                                <v-radio
                                    v-for="tax in addableTaxes"
                                    :key="tax.id"
                                    :value="tax.id"
                                >
                                    <template #label>
                                        <div class="tw-flex">
                                            <div>{{ tax.name }}</div>
                                            <strong class="ml-2">
                                                ({{ tax.rate }}%)
                                            </strong>
                                        </div>
                                    </template>
                                </v-radio>
                            </v-radio-group>
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
                    @click="submit"
                >
                    {{ $t('labels.add') }}
                    <template #loader>
                        <span>
                            {{ $t('labels.adding') }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
