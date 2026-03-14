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

<script>
export default {
    props: {
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
            required: true,
        },
    },
    data() {
        return {
            show: false,
            selectedValue: null,
            currentDocumentNumber: '',
            currentDocumentDate: null,
            dateMenu: false,
        }
    },
    computed: {
        paymentFormText() {
            if (this.paymentForm?.text) {
                return this.paymentForm.text
            }
            const option = this.options.find(
                (opt) => opt.value === this.paymentForm?.value,
            )
            return option?.text || 'No payment set'
        },

        paymentFormColor() {
            if (this.paymentForm?.color) {
                return this.paymentForm.color
            }
            const option = this.options.find(
                (opt) => opt.value === this.paymentForm?.value,
            )
            return option?.color || 'grey'
        },
    },
    methods: {
        openModal() {
            this.show = true
            this.selectedValue = this.paymentForm?.value || null
            this.currentDocumentNumber = this.documentNumber || ''
            this.currentDocumentDate = this.documentDate || null
        },
        closeModal() {
            this.show = false
        },
        save() {
            this.$emit('update', {
                value: this.selectedValue,
                document_number: this.currentDocumentNumber,
                document_date: this.currentDocumentDate,
            })
            this.closeModal()
        },
    },
}
</script>
<style scoped>
.no-radius {
    border-radius: 0 !important;
}
</style>
