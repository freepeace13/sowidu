<script setup>
import { ref, toRef } from 'vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import InvoiceDetailsForm from '~Invoicify/Components/Forms/InvoiceDetailsForm.vue'
import Events from '~Invoicify/Enums/Events'
import { EventBus } from '~Invoicify/Services/EventBus'

const { $t } = useGlobalVariables()

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
    color: {
        type: String,
        default: 'grey darken-2',
    },
    dialogWidth: {
        type: [String, Number],
        default: '90%',
    },
    invoice: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && typeof value.id === 'number'
        },
    },
})

const isTile = toRef(props, 'tile')
const dialogWidth = toRef(props, 'dialogWidth')

const detailsFormRef = ref(null)

const openDetailsForm = () => {
    if (detailsFormRef.value) {
        detailsFormRef.value.show(props.invoice)
    }
}

const onRefresh = () => {
    EventBus.$emit(Events.INVOICE_UPDATED, {
        invoice: props.invoice.id,
    })
}
</script>

<template>
    <div v-if="invoice.can_be_edited">
        <!-- List tile style -->
        <v-list-tile
            v-if="isTile"
            avatar
            @click="openDetailsForm"
        >
            <v-list-tile-avatar>
                <v-icon :color="color">edit</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ $t('invoices.buttons.edit-invoice-details') }}
                </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <!-- Button style -->
        <v-btn
            v-else
            depressed
            color="info"
            @click="openDetailsForm"
        >
            <v-icon left>edit</v-icon>
            {{ $t('invoices.buttons.edit-invoice-details') }}
        </v-btn>

        <!-- Dialog portal -->
        <portal to="edit-invoice-details-dialog">
            <InvoiceDetailsForm
                ref="detailsFormRef"
                :width="dialogWidth"
                @refresh="onRefresh"
            />
        </portal>
    </div>
</template>
