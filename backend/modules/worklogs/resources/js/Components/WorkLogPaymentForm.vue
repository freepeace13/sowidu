<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    paymentForm: {
        type: Object,
        default: () => ({}),
    },
    documentNumber: {
        type: String,
        default: '',
    },
    documentDate: {
        type: String,
        default: null,
    },
    options: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['update'])

const show = ref(false)
const selectedValue = ref(null)
const currentDocumentNumber = ref('')
const currentDocumentDate = ref(null)
const dateMenu = ref(false)

const paymentFormText = computed(() => {
    if (props.paymentForm?.text) return props.paymentForm.text
    const option = props.options.find(
        (opt) => opt.value === props.paymentForm?.value,
    )
    return option?.text || 'No payment set'
})

const paymentFormColor = computed(() => {
    if (props.paymentForm?.color) return props.paymentForm.color
    const option = props.options.find(
        (opt) => opt.value === props.paymentForm?.value,
    )
    return option?.color || 'grey'
})

function openModal() {
    show.value = true
    selectedValue.value = props.paymentForm?.value || null
    currentDocumentNumber.value = props.documentNumber || ''
    currentDocumentDate.value = props.documentDate || null
}

function closeModal() {
    show.value = false
}

function save() {
    emit('update', {
        value: selectedValue.value,
        document_number: currentDocumentNumber.value,
        document_date: currentDocumentDate.value,
    })
    closeModal()
}
</script>

<template>
    <div>
        <v-chip
            :color="paymentFormColor"
            text-color="white"
            small
            class="cursor-pointer no-radius"
            @click="openModal"
        >
            {{ paymentFormText }}
        </v-chip>

        <v-dialog
            v-model="show"
            max-width="420"
        >
            <v-card>
                <v-toolbar
                    flat
                    dense
                >
                    <v-toolbar-title class="text-h6">
                        {{ $t('work_log.labels.form.payment_form') }}
                    </v-toolbar-title>
                    <v-spacer />
                    <v-btn
                        icon
                        @click="closeModal"
                    >
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>

                <v-card-text>
                    <v-select
                        v-model="selectedValue"
                        :items="options"
                        item-value="value"
                        item-text="text"
                        :label="$t('work_log.labels.form.payment_method')"
                        outlined
                        dense
                        class="mt-4"
                    />

                    <v-text-field
                        v-model="currentDocumentNumber"
                        :label="$t('work_log.labels.form.document_number')"
                        outlined
                        dense
                        class="mt-4"
                    />

                    <v-menu
                        v-model="dateMenu"
                        :close-on-content-click="true"
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                    >
                        <template #activator="{ on }">
                            <v-text-field
                                v-model="currentDocumentDate"
                                :label="
                                    $t('work_log.labels.form.document_date')
                                "
                                readonly
                                outlined
                                dense
                                class="mt-4"
                                v-on="on"
                            />
                        </template>
                        <v-date-picker
                            v-model="currentDocumentDate"
                            @input="dateMenu = false"
                        />
                    </v-menu>
                </v-card-text>

                <v-card-actions>
                    <v-spacer />
                    <v-btn
                        text
                        @click="closeModal"
                    >
                        {{ $t('work_log.labels.cancel-button') }}
                    </v-btn>
                    <v-btn
                        color="primary"
                        dark
                        @click="save"
                    >
                        {{ $t('work_log.labels.save-button') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<style scoped>
.no-radius {
    border-radius: 0 !important;
}
</style>
