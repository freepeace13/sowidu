<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useClipboard } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import InvoiceNotesForm from './InvoiceNotesForm.vue'
import InvoiceSubjectForm from './InvoiceSubjectForm.vue'
import EditInvoiceDetailsForm from './EditInvoiceDetailsForm.vue'

defineExpose({
    refresh,
})

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    isEditable: {
        type: Boolean,
        required: true,
    },
})

defineEmits([
    'click:download',
    'click:edit-notes',
    'click:edit-subject-description',
])

const { $route, $root, $t } = useGlobalVariables()

const dialog = ref(false)
const invoiceSubjectFormRef = ref(null)
const invoiceNotesFormRef = ref(null)
const editInvoiceDetailsFormRef = ref(null)

const { copy, copied } = useClipboard({ legacy: true })

const iframeLoaded = ref(false)
const refreshKey = ref(0)

const iframeSrc = computed(() => {
    return `${$route('invoice.pdf.stream', {
        invoice: props.invoice,
    })}?refresh=${refreshKey.value}`
})

watch(copied, (new_value) => {
    if (new_value) {
        $root.$emit('flash.success', $t('invoices.message.copied-to-clipboard'))
    }
})

watch(dialog, (new_value) => {
    if (new_value) {
        iframeLoaded.value = false
        refreshKey.value++
    }
})

function refresh() {
    iframeLoaded.value = false
    refreshKey.value++
}
</script>
<template>
    <v-dialog
        v-model="dialog"
        fullscreen
        hide-overlay
        transition="dialog-bottom-transition"
    >
        <InvoiceSubjectForm
            ref="invoiceSubjectFormRef"
            @refresh="refresh"
        />
        <InvoiceNotesForm
            ref="invoiceNotesFormRef"
            :invoice="invoice"
            @refresh="refresh"
        />
        <EditInvoiceDetailsForm
            ref="editInvoiceDetailsFormRef"
            @refresh="refresh"
        />
        <template #activator="{ on }">
            <v-btn
                color="grey-lighter"
                class=""
                depressed
                v-on="on"
            >
                <v-icon left>visibility</v-icon>
                {{ $t('invoices.buttons.preview') }}
            </v-btn>
        </template>
        <v-card>
            <v-toolbar
                dark
                color="primary"
            >
                <v-btn
                    icon
                    dark
                    @click="dialog = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('invoices.labels.invoice-preview') }}
                </v-toolbar-title>
                <v-spacer />
                <v-toolbar-items>
                    <v-menu offset-y>
                        <template #activator="{ on }">
                            <v-btn
                                depressed
                                color="info"
                                :disabled="!iframeLoaded || !isEditable"
                                v-on="on"
                            >
                                <v-icon left>edit</v-icon>
                                {{ $t('hints.edit') }}
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-tile
                                @click="invoiceSubjectFormRef.show(invoice)"
                            >
                                <v-list-tile-title>
                                    {{
                                        $t(
                                            'invoices.buttons.edit-subject-description',
                                        )
                                    }}
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="invoiceNotesFormRef.show()">
                                <v-list-tile-title>
                                    {{ $t('invoices.buttons.edit-notes') }}
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile
                                @click="editInvoiceDetailsFormRef.show(invoice)"
                            >
                                <v-list-tile-title>
                                    {{
                                        $t(
                                            'invoices.buttons.edit-invoice-details',
                                        )
                                    }}
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>

                    <v-btn
                        color="primary"
                        depressed
                        :disabled="!iframeLoaded"
                        @click="
                            copy(
                                $route('invoice.pdf.download', {
                                    invoice: invoice.uuid,
                                }),
                            )
                        "
                    >
                        <v-icon left>share</v-icon>
                        {{ $t('buttons.share') }}
                    </v-btn>

                    <v-btn
                        color="primary"
                        depressed
                        :disabled="!iframeLoaded"
                        @click="$emit('click:download')"
                    >
                        <v-icon left>file_download</v-icon>
                        {{ $t('buttons.download-pdf') }}
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>

            <v-divider />

            <v-card-text>
                <v-progress-circular
                    v-if="dialog && !iframeLoaded"
                    indeterminate
                    color="primary"
                    class="mx-auto d-block my-8"
                    size="50"
                />
                <iframe
                    v-if="dialog"
                    :src="iframeSrc"
                    style="width: 100%; height: 93vh; border: none"
                    @load="iframeLoaded = true"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>
