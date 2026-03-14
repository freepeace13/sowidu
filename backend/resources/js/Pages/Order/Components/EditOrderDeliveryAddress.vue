<script setup>
import { ref } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import AddressAutocomplete from '@/Components/AddressAutocomplete.vue'
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $t, $route } = useGlobalVariables()

const order = ref(null)
const isShow = ref(false)
const form = useForm({
    delivery_address: null,
})

defineExpose({
    show,
})

function show(orderData) {
    order.value = orderData

    form.delivery_address = orderData.delivery_address

    isShow.value = true
}

function close() {
    form.reset()
    isShow.value = false
    order.value = null
}

function submit() {
    form.patch($route('orders.update', { order: order.value.id }), {
        preserveScroll: true,
        only: ['order', 'errors'],
        onSuccess: () => {
            close()
        },
    })
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
                    {{ $t('order.labels.edit-delivery-address') }}
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
                        <v-flex xs12>
                            <AddressAutocomplete
                                v-model="form.delivery_address"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.delivery-address')"
                                :error-messages="form.errors.delivery_address"
                                :hide-details="!form.errors.delivery_address"
                            />
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
                    {{ $t('buttons.update') }}
                    <template #loader>
                        <span> {{ $t('buttons.updating') }}... </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
