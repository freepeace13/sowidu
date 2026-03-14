<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import { useDebounceFn, watchDebounced } from '@vueuse/core'
import axios from 'axios'
import { onMounted, watch, ref, reactive } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import InvoiceDeductionRow from './InvoiceDeductionRow.vue'

defineExpose({ reset })

const { $route } = useGlobalVariables()

const props = defineProps({ invoice: Object, close: Function })

const emit = defineEmits(['close'])
const items = ref([])
const form = useForm({
    invoices: [],
})

const dummy = ref([])
const filters = reactive({
    q: null,
    type: null,
    count: 15,
    page: 1,
})

const isFetching = ref(false)
const pagination = ref({
    page: 1,
    descending: false,
    count: 15,
    sortBy: null,
    total: 0,
})

watchDebounced(filters, () => {
    fetchItems()
})

onMounted(() => {
    reset()
    fetchItems()
})

async function reset() {
    form.reset()
    form.clearErrors()

    filters.q = null

    await fetchItems()
}

const fetchItems = useDebounceFn(async () => {
    isFetching.value = true
    items.value = []
    const { page } = pagination.value
    const params = {
        ...filters,
        page,
        invoice: props.invoice.id,
        order: props.invoice.order.id,
    }

    const {
        data: { data, per_page, current_page, total },
    } = await axios.get($route('json.order.invoices.deductable', params))

    pagination.value = {
        count: per_page,
        page: current_page,
        total: total,
        descending: false,
        sortBy: null,
    }

    items.value = data
    isFetching.value = false
}, 500)

watch(dummy, (value) => (form.invoices = value.map((invoice) => invoice.id)))

function paginate(payload) {
    pagination.value.page = payload.page
    //pagination.value = payload
    fetchItems()
}

function submit() {
    form.post($route('invoices.deduct', { invoice: props.invoice.id }), {
        preserveScroll: true,
        only: ['invoiceSummary'],
        onSuccess: async () => {
            await fetchItems()

            form.reset()
            dummy.value = []
            emit('close')
        },
    })
}

const headers = [
    { text: 'Invoice No.', sortable: false },
    { text: 'Client', sortable: false },
    { text: 'Order Number', sortable: false },
    { text: 'Status', sortable: false },
    { text: 'Delivery Address', sortable: false },
    { text: 'Delivery Date', sortable: false },
]
</script>
<template>
    <div class="tw-mt-10">
        <v-container
            grid-list-lg
            fluid
            pa-2
        >
            <v-layout
                row
                wrap
            >
                <v-text-field
                    v-model="filters.q"
                    color="primary"
                    :loading="isFetching"
                    :label="$t('labels.search')"
                    outline
                    :hide-details="true"
                />
                <v-flex
                    xs12
                    class="tw-flex tw-flex-col !tw-p-0 !tw-mt-5"
                >
                    <v-data-table
                        v-model="dummy"
                        :headers="headers"
                        show-select
                        :items="items"
                        :loading="isFetching"
                        :total-items="pagination.total"
                        :rows-per-page-items="[pagination?.count ?? 15]"
                        select-all
                        item-key="id"
                        item-value="id"
                        selectable-key="id"
                        class="elevation-1 px-0 py-2"
                        @toggle-select-all="(value) => selectAll(value)"
                        @update:pagination="(payload) => paginate(payload)"
                    >
                        <template #items="itemProps">
                            <InvoiceDeductionRow
                                v-model="form.invoices"
                                :invoice="itemProps.item"
                                @click:row="
                                    () =>
                                        (itemProps.selected =
                                            !itemProps.selected)
                                "
                            >
                                <template #checkbox>
                                    <td>
                                        <v-checkbox
                                            v-model="form.invoices"
                                            primary
                                            hide-details
                                            :value="itemProps.item.id"
                                        />
                                    </td>
                                </template>
                            </InvoiceDeductionRow>
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
        </v-container>
        <v-divider />
        <v-card-actions class="px-4 py-4">
            <v-spacer />
            <v-btn
                :disabled="form.processing"
                outline
                depressed
                @click="close"
            >
                {{ $t('buttons.close') }}
            </v-btn>
            <SubmitButton
                :loading="form.processing"
                :disabled="form.processing || !form.invoices.length"
                @click="submit"
            >
                {{ $t('invoices.labels.add-deduction') }}
            </SubmitButton>
        </v-card-actions>
    </div>
</template>
