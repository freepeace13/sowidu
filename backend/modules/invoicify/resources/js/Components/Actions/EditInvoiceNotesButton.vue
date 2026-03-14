<script setup>
import { ref, toRef } from 'vue'
import InvoiceNotesForm from '../Forms/InvoiceNotesForm.vue'
import Events from '~Invoicify/Enums/Events'
import { EventBus } from '~Invoicify/Services/EventBus'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

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
        default: '35%',
    },
    invoice: {
        type: Object,
        required: true,
        validator: (value) => value && typeof value.id === 'number',
    },
})

const isTile = toRef(props, 'tile')
const dialogWidth = toRef(props, 'dialogWidth')

const notesFormRef = ref(null)

const onRefresh = () => {
    EventBus.$emit(Events.INVOICE_UPDATED, {
        invoice: props.invoice.id,
    })
}
</script>

<template>
    <div v-if="invoice.can_be_edited">
        <v-list-tile
            v-if="isTile"
            avatar
            @click="notesFormRef.show()"
        >
            <v-list-tile-avatar>
                <v-icon :color="color">note</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>{{
                    $t('invoices.buttons.edit-notes')
                }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-btn
            v-else
            depressed
            color="info"
            @click="notesFormRef.show()"
        >
            <v-icon left>note</v-icon>
            {{ $t('invoices.buttons.edit-notes') }}
        </v-btn>

        <portal to="invoice-notes-dialog">
            <InvoiceNotesForm
                ref="notesFormRef"
                :invoice="invoice"
                :width="dialogWidth"
                @refresh="onRefresh"
            />
        </portal>
    </div>
</template>
