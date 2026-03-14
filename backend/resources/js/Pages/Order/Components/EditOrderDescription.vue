<script setup>
import { ref } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'

const { $t, $route } = useGlobalVariables()

const order = ref(null)
const isShow = ref(false)
const form = useForm({
    description: null,
})

defineExpose({
    show,
})

function show(orderData) {
    order.value = orderData
    form.description = orderData.description

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
        only: ['order'],
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
                    {{ $t('order.labels.edit-description') }}
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
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :label="$t('order.labels.inputs.description')"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                outline
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
