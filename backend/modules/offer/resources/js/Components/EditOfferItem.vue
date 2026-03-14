<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps, numberFormat } from '@/Composables/useUtils'
import { useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'

defineExpose({ show })

const { $t, $route } = useGlobalVariables()

const isShow = ref(false)
const offerItem = ref(null)

const form = useForm({
    quantity: null,
    price: null,
})
const details = computed(() => offerItem.value?.details)
const subtotal = computed(() => {
    const price = form.price
    const quantity = form.quantity

    if (!price || !quantity) return null

    return numberFormat(quantity * price)
})

const quantityOrPriceChanged = computed(
    () =>
        parseFloat(form.quantity) !== parseFloat(offerItem.value?.quantity) ||
        parseFloat(form.price) !== parseFloat(offerItem.value?.price),
)

function show(item) {
    if (!item) return

    offerItem.value = item

    form.quantity = item.quantity
    form.price = item.price

    isShow.value = true
}

function close() {
    isShow.value = false
    form.reset()
    offerItem.value = null
}

function submit() {
    const offer = getPageProps('offer')

    const item = offerItem.value
    form.patch(
        $route('offers.items.update', {
            offer,
            item,
        }),
        {
            only: ['offer', 'items'],
            onSuccess: () => {
                close()
            },
        },
    )
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        max-width="750"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('offer.items.labels.edit_item') }}
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
                            class="tile px-0 tw-max-w-[90%]"
                        >
                            <v-list-tile-avatar>
                                <v-avatar>
                                    <v-img :src="details?.media?.url" />
                                </v-avatar>
                            </v-list-tile-avatar>
                            <v-list-tile-content>
                                <v-list-tile-title class="tw-max-w-full">
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
                            :label="$t('offer.items.inputs.quantity')"
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
                        mt-3
                        xs5
                    >
                        <v-text-field
                            v-model="form.price"
                            outline
                            full-width
                            :loading="form.processing"
                            :error-messages="form.errors.price"
                            :hide-details="!form.errors.price"
                            :label="$t('offer.items.inputs.price')"
                            class="required-input"
                            required
                            type="number"
                        />
                    </v-flex>
                    <v-flex
                        offset-xs7
                        xs5
                        mt-3
                    >
                        <div class="tw-flex tw-items-center tw-justify-between">
                            <v-label>
                                <span class="tw-text-xl">
                                    {{ $t('offer.items.inputs.total') }}:
                                </span>
                            </v-label>
                            <div
                                :class="[
                                    'tw-text-xl',
                                    'ml-2',
                                    {
                                        'tw-text-red-500':
                                            quantityOrPriceChanged,
                                    },
                                ]"
                            >
                                {{ subtotal }}
                                {{ offerItem?.currency?.symbol }}
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
