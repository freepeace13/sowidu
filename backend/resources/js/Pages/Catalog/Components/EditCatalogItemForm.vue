<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps, numberFormat } from '@/Composables/useUtils'
import { useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'

const props = defineProps({
    order: {
        required: false,
        type: [Object],
        default: () => ({}),
    },
})

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const isShow = ref(false)
const item = ref(null)

const form = useForm({
    quantity: null,
})
const defaultCurrency = computed(() => getPageProps('defaults.currency.symbol'))

const details = computed(() => item.value?.details)

function show(catalogItem) {
    item.value = catalogItem

    form.quantity = catalogItem.quantity

    isShow.value = true
}

function close() {
    isShow.value = false
    form.reset()
    item.value = null
}

function submit() {
    const order = props.order
    const orderProduct = item.value

    form.patch($route('orders.show.products.update', { order, orderProduct }), {
        only: ['totalPrice', 'products'],
        onSuccess: () => {
            close()
        },
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('catalog.buttons.edit-quantity') }}
            </v-card-title>

            <v-card-text>
                <v-layout
                    row
                    wrap
                    grid-list-md
                >
                    <v-flex
                        xs7
                        class="tw-flex tw-items-end"
                    >
                        <v-list-tile
                            px-0
                            class="tile px-0"
                        >
                            <v-list-tile-avatar>
                                <v-avatar>
                                    <v-img :src="details?.media?.url" />
                                </v-avatar>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title class="">
                                    {{ details?.name }}
                                </v-list-tile-title>
                                <v-list-tile-sub-title
                                    class="caption text-truncate"
                                >
                                    {{ details?.type?.name }}
                                </v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-flex>
                    <v-flex xs5>
                        <v-text-field
                            v-model="form.quantity"
                            outline
                            full-width
                            :loading="form.processing"
                            :error-messages="form.errors.quantity"
                            :hide-details="!form.errors.quantity"
                            :label="$t('invoices.labels.quantity')"
                            class="required-input"
                            required
                            type="number"
                        />
                    </v-flex>
                    <v-flex
                        xs7
                        mt-3
                    >
                        {{ details?.description }}
                    </v-flex>
                    <v-flex
                        xs5
                        mt-3
                    >
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <v-label>
                                <span class="">
                                    {{
                                        $t('catalog.labels.item.selling-price')
                                    }}:
                                </span>
                            </v-label>
                            <div class="ml-2 tw-text-lg">
                                {{ numberFormat(details?.selling_price) }}
                                {{ defaultCurrency }}
                            </div>
                        </div>
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <v-label>
                                <span class="">
                                    {{
                                        $t(
                                            'catalog.labels.item.purchasing-price',
                                        )
                                    }}:
                                </span>
                            </v-label>
                            <div class="ml-2 tw-text-lg">
                                {{ numberFormat(details?.purchasing_price) }}
                                {{ defaultCurrency }}
                            </div>
                        </div>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions class="pa-3 mt-2">
                <v-btn
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="close"
                >
                    {{ $t('buttons.close') }}
                </v-btn>
                <v-spacer />

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
