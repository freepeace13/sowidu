<script setup>
import { computed, ref } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const isShow = ref(false)
const invoiceItem = ref(null)

const form = useForm({
    price: null,
})
const details = computed(() => invoiceItem.value?.details)
function show(item) {
    if (!item) return

    invoiceItem.value = item
    form.price = item.price
    isShow.value = true
}

function close() {
    isShow.value = false
    form.reset()
}

function submit() {
    const item = invoiceItem.value
    form.patch(
        $route('invoices.items.update_price', {
            invoice: item.invoice_id,
            item,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['items', 'totalPrice', 'invoiceSummary'],
            onFinish: () => {
                close()
            },
        },
    )
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('invoices.items.labels.edit-employee-rate') }}
            </v-card-title>

            <v-card-text>
                <v-layout
                    row
                    wrap
                >
                    <v-flex xs12>
                        <v-list-tile
                            px-0
                            class="tile px-0"
                        >
                            <v-list-tile-avatar>
                                <v-avatar>
                                    <v-img :src="details?.media_url" />
                                </v-avatar>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title class="">
                                    {{ details?.user?.name }}
                                </v-list-tile-title>
                                <v-list-tile-sub-title class="caption">
                                    {{ details?.user.email }}
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                            <v-list-tile-action>
                                <v-layout
                                    row
                                    wrap
                                    justify-end
                                >
                                    <v-flex xs12>
                                        <v-text-field
                                            v-model="form.price"
                                            outline
                                            full-width
                                            :items="[]"
                                            :loading="form.processing"
                                            :error-messages="form.errors.price"
                                            :hide-details="!form.errors.price"
                                            :label="
                                                $t('invoices.items.labels.rate')
                                            "
                                            solo
                                            class="required-input small"
                                            required
                                        />
                                    </v-flex>
                                </v-layout>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn @click="close">
                    {{ $t('buttons.close') }}
                </v-btn>
                <SubmitButton
                    :is-processing="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.save') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
