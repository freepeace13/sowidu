<script setup>
import { ref } from 'vue'
import ModalButtonClose from '@/Apps/Shared/Components/ModalButtonClose.vue'
import InvoiceDeductionForm from './DeductionInvoiceForm.vue'
import ManualDeductionForm from './DeductionManualForm.vue'
defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Object,
        required: true,
    },
})
const isShow = ref(false)

const show = () => {
    isShow.value = true
}

const close = () => {
    isShow.value = false
}

defineExpose({
    show,
})
const active = ref('invoice_deduction')
</script>

<template>
    <v-dialog
        v-model="isShow"
        persistent
        lazy
        :max-width="active !== 'invoice_deduction' ? '640px' : '100%'"
        :full-width="false"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $t('invoices.labels.add-deduction') }}
                </v-toolbar-title>
                <v-spacer />
                <ModalButtonClose @click.native="close" />
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-tabs
                    v-model="active"
                    icons-and-text
                    color="transparent"
                >
                    <v-tabs-slider color="primary" />
                    <v-tab href="#invoice_deduction">
                        <div class="tw-text-lg">
                            {{ $t('invoices.labels.invoice-deduction') }}
                        </div>
                        <v-icon color="indigo">list</v-icon>
                    </v-tab>
                    <v-tab href="#manual_deduction">
                        <div class="tw-text-lg">
                            {{ $t('invoices.labels.manual-deduction') }}
                        </div>
                        <v-icon color="indigo">edit_note</v-icon>
                    </v-tab>
                    <v-tab-item value="invoice_deduction">
                        <InvoiceDeductionForm
                            :active-tab="active"
                            :invoice="invoice"
                            :permissions="permissions"
                            :close="close"
                        />
                    </v-tab-item>
                    <v-tab-item value="manual_deduction">
                        <ManualDeductionForm
                            :active-tab="active"
                            :invoice="invoice"
                            :permissions="permissions"
                            :close="close"
                        />
                    </v-tab-item>
                </v-tabs>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
