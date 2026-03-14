<script setup>
import AppAvatar from '@/Components/AppAvatar.vue'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { useDateFormat } from '@/Composables/useDayJs'
import { useForm, usePage } from '@inertiajs/vue2'
import { useDebounceFn, watchDebounced } from '@vueuse/core'
import axios from 'axios'
import { toRef } from 'vue'
import { getCurrentInstance } from 'vue'
import { watch } from 'vue'
import { reactive } from 'vue'
import { ref } from 'vue'

const tabProps = defineProps({
    activeTab: {
        required: true,
        type: String,
    },
})

defineEmits(['close'])

const order = usePage().props.invoice.order
const items = ref([])
const activeTab = toRef(tabProps, 'activeTab')
const form = useForm({
    delivery_tickets: [],
})

const filters = reactive({
    q: null,
})

const app = getCurrentInstance()
const isFetching = ref(false)
const pagination = ref({
    page: 1,
    descending: false,
    rowsPerPage: 10,
    totalItems: 0,
})

const headers = [
    { text: 'Ticket No.', sortable: false },
    { text: 'External ID', sortable: false },
    { text: 'Deliverer', sortable: false },
    { text: 'Order Number', sortable: false },
    { text: 'Delivery Address', sortable: false },
    { text: 'Delivery Date', sortable: false },
]

watch(activeTab, (tab) => {
    if (tab != 'delivery_tickets') {
        return
    }

    reset()
    fetchItems()
})

watchDebounced(filters, () => {
    fetchItems()
})

function reset() {
    form.reset()
    form.clearErrors()

    filters.q = null
}

const fetchItems = useDebounceFn(async () => {
    if (activeTab.value != 'delivery_tickets') {
        return
    }

    isFetching.value = true

    const { page } = pagination

    const params = { ...filters, page, order: order.id }

    const {
        data: { data, per_page, current_page, total },
    } = await axios.get(window.route('json.delivery_tickets.index', params))

    pagination.value = {
        rowsPerPage: per_page,
        page: current_page,
        totalItems: total,
        descending: false,
    }

    items.value = data
    isFetching.value = false
}, 500)

function paginate(payload) {
    pagination.value = payload
    fetchItems()
}

function submit() {
    const invoice = usePage().props.invoice

    form.transform((data) => ({
        ...data,
        delivery_tickets: data.delivery_tickets.map(({ id }) => id),
    })).post(window.route('invoices.items.store', { invoice }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
        only: ['items', 'totalPrice', 'flash'],
        onError: (errors) => app.proxy.$root.$emit('flash.validation', errors),
    })
}
</script>
<template>
    <v-card flat>
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
                        <v-text-field
                            v-model="filters.q"
                            color="primary"
                            :loading="isFetching"
                            :label="$t('labels.search')"
                            outline
                            :hide-details="true"
                        />
                    </v-flex>
                    <v-flex
                        xs12
                        class="tw-flex tw-flex-col tw-gap-y-3"
                    >
                        <v-data-table
                            v-model="form.delivery_tickets"
                            :headers="headers"
                            :items="items"
                            :loading="isFetching"
                            :total-items="pagination.totalItems"
                            :rows-per-page-items="[
                                pagination?.rowsPerPage ?? 10,
                            ]"
                            select-all
                            item-key="id"
                            class="elevation-1 px-0 py-2"
                            @update:pagination="(payload) => paginate(payload)"
                        >
                            <template #items="props">
                                <tr @click="props.selected = !props.selected">
                                    <td>
                                        <v-checkbox
                                            v-model="props.selected"
                                            primary
                                            hide-details
                                        />
                                    </td>
                                    <td>
                                        <a
                                            class="info--text hover:tw-underline tw-font-semibold"
                                            @click="
                                                $emit(
                                                    'click:show-details',
                                                    props.item.id,
                                                )
                                            "
                                        >
                                            {{
                                                props.item?.internal_id ??
                                                props.item.id
                                            }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ props.item?.external_id }}
                                    </td>
                                    <td class="">
                                        <div
                                            class="tw-flex tw-items-center tw-gap-x-2"
                                        >
                                            <AppAvatar
                                                :avatar="
                                                    props.item.deliverer
                                                        .column_values.photo
                                                "
                                            />
                                            {{
                                                props.item.deliverer
                                                    .column_values.name
                                            }}
                                        </div>
                                    </td>
                                    <td>
                                        <a
                                            class="info--text hover:tw-underline tw-font-semibold"
                                            :href="
                                                $route('orders.show', {
                                                    order: props.item.order?.id,
                                                })
                                            "
                                            target="_blank"
                                        >
                                            {{
                                                props.item.order
                                                    ?.order_number ?? '--'
                                            }}
                                        </a>
                                    </td>
                                    <td
                                        class="tw-table-cell !tw-h-auto md:tw-h-12 px-2"
                                    >
                                        <div
                                            class="tw-flex tw-items-center tw-justify-between"
                                        >
                                            <div class="py-1">
                                                {{
                                                    props.item.delivery_address
                                                        .full
                                                }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{
                                            useDateFormat(
                                                props.item.delivery_date,
                                                'DD.MM.YYYY',
                                            )
                                        }}
                                    </td>
                                </tr>
                            </template>
                        </v-data-table>
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
                @click="$emit('close')"
            >
                {{ $t('buttons.close') }}
            </v-btn>
            <SubmitButton
                :loading="form.processing"
                :disabled="form.processing || !form.delivery_tickets.length"
                @click="submit"
            >
                {{ $t('invoices.buttons.add-to-invoice') }}
            </SubmitButton>
        </v-card-actions>
    </v-card>
</template>
