<script setup>
import { router, usePage } from '@inertiajs/vue2'
import { ref, toRef } from 'vue'
import ConfirmsAction from '~Shared/Components/ConfirmsAction.vue'
import { canAll } from '~Shared/Composables/useAuth'
import useGlobalVariables from '~Shared/Composables/useGlobalVariables'

const props = defineProps({
    tile: {
        type: Boolean,
        default: false,
    },
})

const isTile = toRef(props, 'tile')
const { $t, $route } = useGlobalVariables()
const invoice = usePage().props.invoice
const isDeleting = ref(false)

const handleDeleteInvoice = () => {
    router.delete(
        $route('invoicify.destroy', {
            invoice: invoice.id,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            onStart: () => (isDeleting.value = true),
            onFinish: () => (isDeleting.value = false),
            onSuccess: () => {
                router.get($route('invoices.index'))
            },
        },
    )
}
</script>

<template>
    <ConfirmsAction
        v-if="canAll(['can_delete_invoice', 'invoice_is_editable'])"
        type="delete"
        :title="$t('labels.delete')"
        :question="$t('invoices.message.confirm_deleting')"
        @confirm="handleDeleteInvoice"
    >
        <template #default="{ on, attrs }">
            <v-list-tile
                v-if="isTile"
                avatar
                :disabled="isDeleting"
                v-bind="attrs"
                color="error"
                v-on="on"
            >
                <v-list-tile-avatar>
                    <v-icon>delete</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>{{
                        $t('buttons.delete')
                    }}</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>

            <v-btn
                v-else
                depressed
                :disabled="isDeleting"
                :loading="isDeleting"
                color="error"
                v-bind="attrs"
                v-on="on"
            >
                <v-icon left>delete</v-icon>
                {{ $t('buttons.delete') }}
            </v-btn>
        </template>
    </ConfirmsAction>
</template>
