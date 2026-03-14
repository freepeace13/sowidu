<script setup>
import { ref, toRef } from 'vue'
import InvoiceSubjectForm from '~Invoicify/Components/Forms/InvoiceSubjectForm.vue'
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
        validator: (value) => {
            return value && typeof value.id === 'number'
        },
    },
})

const isTile = toRef(props, 'tile')
const dialogWidth = toRef(props, 'dialogWidth')

const subjectFormRef = ref(null)

const onRefresh = () => {
    EventBus.$emit(Events.INVOICE_UPDATED, {
        invoice: props.invoice.id,
    })
}
</script>

<template>
    <div>
        <v-list-tile
            v-if="isTile"
            avatar
            @click="subjectFormRef.show(invoice)"
        >
            <v-list-tile-avatar>
                <v-icon :color="color">edit</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>{{ $t('hints.edit') }}</v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-btn
            v-else
            depressed
            color="info"
            @click="subjectFormRef.show(invoice)"
        >
            <v-icon left>edit</v-icon>
            {{ $t('hints.edit') }}
        </v-btn>
        <portal to="edit-pdf-properties-dialog">
            <InvoiceSubjectForm
                ref="subjectFormRef"
                :width="dialogWidth"
                @refresh="onRefresh"
            />
        </portal>
    </div>
</template>
