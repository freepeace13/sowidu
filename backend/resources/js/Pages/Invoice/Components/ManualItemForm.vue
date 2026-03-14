<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps } from '@/Composables/useUtils'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { computed, nextTick, ref } from 'vue'

defineExpose({ show })

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['refresh'])

const { $t, $root, $route } = useGlobalVariables()

const form = useForm({
    name: null,
    type: null,
    internal_id: null,
    vendor_id: null,
    unit: null,
    purchasing_price: null,
    selling_price: null,
    description: null,
    quantity: 1,
})

const isShow = ref(false)
const item = ref(null)
const searchType = ref('')
const itemTypes = ref([])

const isCreating = computed(() => !item.value)

const loadingText = computed(() => {
    return isCreating.value ? $t('buttons.creating') : $t('buttons.updating')
})

const unitOptions = computed(() => getPageProps('unitOptions'))
const currency = computed(() => getPageProps('currency'))

function show(item = null) {
    form.reset()
    itemTypes.value = getPageProps('itemTypeOptions')

    if (item) {
        item.value = item

        form.name = item.name
        form.internal_id = item.internal_id
        form.vendor_id = item.vendor_id
        form.unit = item.unit
        form.purchasing_price = item.purchasing_price
        form.selling_price = item.selling_price
        form.description = item.description

        form.type = {
            text: item.type.name,
            value: item.type.id,
        }
    }

    isShow.value = true
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    item.value = null
    searchType.value = ''
    form.reset()
    form.clearErrors()
}

function submit() {
    const invoice = props.invoice

    let route = $route('invoices.manual_items.store', {
        invoice,
    })

    if (!isCreating.value) {
        route = $route('invoices.manual_items.update', {
            item: item.value,
            invoice,
        })
    }

    const method = isCreating.value ? 'post' : 'patch'
    form.transform((data) => ({
        ...data,
        type: data.type?.text,
    }))
    form[method](route, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('refresh')
            close()
        },
        onError: (errors) => $root.$emit('flash.validation', errors),
    })
}

function addNewItemType() {
    const searchText = searchType.value.trim()
    const options = itemTypes.value
    const itemNotFound = !options.find(
        (item) => item.text.toLowerCase() === searchText.toLowerCase(),
    )
    if (itemNotFound && searchText.length) {
        const newOption = {
            text: searchText,
            value: itemTypes.value.length + 1,
        }
        itemTypes.value = [...itemTypes.value, newOption]

        nextTick(() => {
            form.type = newOption
        })
    }
}
</script>
<template>
    <!-- eslint-disable vue/no-v-html -->
    <v-dialog
        v-model="isShow"
        persistent
        lazy
        width="600"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        isCreating
                            ? $t('invoices.manual-item.labels.add')
                            : $t('invoices.manual-item.labels.update')
                    }}
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
                        <v-flex
                            xs12
                            class="tw-flex tw-flex-col tw-gap-y-3"
                        >
                            <v-text-field
                                v-model="form.name"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.name"
                                :label="$t('catalog.labels.item.item-name')"
                                outline
                                :hide-details="!form.errors.name"
                                required
                                class="required-input"
                            />
                        </v-flex>
                        <v-flex xs6>
                            <v-combobox
                                v-model="form.type"
                                :search-input.sync="searchType"
                                outline
                                full-width
                                append-icon="none"
                                :items="itemTypes"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.type"
                                :hide-details="!form.errors.type"
                                :label="$t('catalog.labels.item.type')"
                                class="required-input"
                                required
                                :hide-no-data="!searchType"
                                :menu-props="{ closeOnContentClick: true }"
                            >
                                <template #item="data">
                                    <v-list-tile
                                        @click="() => (form.type = data.item)"
                                    >
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{ data.item.text }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                                <template #no-data>
                                    <v-list-tile @click="addNewItemType">
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                <v-icon color="primary">
                                                    add
                                                </v-icon>
                                                {{
                                                    $t('buttons.create-new-one')
                                                }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </v-combobox>
                        </v-flex>

                        <v-flex sm6>
                            <v-select
                                v-model="form.unit"
                                outline
                                full-width
                                :items="unitOptions"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.unit"
                                :hide-details="!form.errors.unit"
                                :label="$t('catalog.labels.item.unit')"
                                class="required-input"
                                required
                            />
                        </v-flex>

                        <v-flex sm12>
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.quantity"
                                        color="primary"
                                        outline
                                        type="number"
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="form.errors.quantity"
                                        :label="$t('invoices.labels.quantity')"
                                        :hide-details="!form.errors.quantity"
                                    />
                                </v-flex>
                            </v-layout>
                        </v-flex>

                        <v-flex xs12>
                            <v-layout
                                row
                                wrap
                            >
                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.purchasing_price"
                                        color="primary"
                                        outline
                                        type="number"
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="
                                            form.errors.purchasing_price
                                        "
                                        :label="
                                            $t(
                                                'catalog.labels.item.purchasing-price',
                                            )
                                        "
                                        :hide-details="
                                            !form.errors.purchasing_price
                                        "
                                    >
                                        <template #prepend-inner>
                                            <div class="tw-font-semibold">
                                                {{ currency?.symbol }}
                                            </div>
                                        </template>
                                    </v-text-field>
                                </v-flex>

                                <v-flex sm6>
                                    <v-text-field
                                        v-model="form.selling_price"
                                        color="primary"
                                        outline
                                        type="number"
                                        class="required-input"
                                        :disabled="form.processing"
                                        :loading="form.processing"
                                        :error-messages="
                                            form.errors.selling_price
                                        "
                                        :label="
                                            $t(
                                                'catalog.labels.item.selling-price',
                                            )
                                        "
                                        :hide-details="
                                            !form.errors.selling_price
                                        "
                                    >
                                        <template #prepend-inner>
                                            <div class="tw-font-semibold">
                                                {{ currency?.symbol }}
                                            </div>
                                        </template>
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :label="$t('catalog.labels.item.description')"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                outline
                                class="required-input"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4 py-4">
                <v-spacer />
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t(isCreating ? 'buttons.create' : 'buttons.update') }}
                    <template #loader>
                        <span> {{ loadingText }}... </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
