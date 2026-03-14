<script setup>
import { ref, toRef } from 'vue'
import ManualItemForm from '../Forms/ManualItemForm.vue'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'
import { usePage } from '@inertiajs/vue2'
import Events from '~Invoicify/Enums/Events'
import { EventBus } from '~Invoicify/Services/EventBus'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
})

const { $t } = useGlobalVariables()
const { invoice } = usePage().props
const isTile = toRef(props, 'tile')
const manualItemFormRef = ref(null)

const onRefresh = () => {
    EventBus.$emit(Events.INVOICE_UPDATED, { invoice: invoice.id })
}

const openManualItemForm = () => {
    manualItemFormRef.value.show()
}
</script>

<template>
    <div v-if="invoice.can_be_edited">
        <v-list-tile
            v-if="isTile"
            avatar
            @click="openManualItemForm"
        >
            <v-list-tile-avatar>
                <v-icon>add</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ $t('invoices.buttons.add-manual-item') }}
                </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-btn
            v-else
            depressed
            @click="openManualItemForm"
        >
            <v-icon left>add</v-icon>
            {{ $t('invoices.buttons.add-manual-item') }}
        </v-btn>

        <portal to="manual-item-dialog">
            <ManualItemForm
                ref="manualItemFormRef"
                :invoice="invoice"
                @refresh="onRefresh"
            />
        </portal>
    </div>
</template>
