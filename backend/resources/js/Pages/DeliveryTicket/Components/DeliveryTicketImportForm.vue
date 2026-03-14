<script setup>
import { ref, computed } from 'vue'
import SubmitButton from '@components/Forms/SubmitButton.vue'

import useGlobalVariables from '../../../Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import DeliveryTicketReader from '@/Modules/DeliveryTicketReader'
import RawTextParser from '../../../Modules/RawTextParser'
import XLSParser from '../../../Modules/XlsParser'

const props = defineProps({
    order: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['refresh', 'flash.validation'])

const { $t, $route } = useGlobalVariables()

const form = useForm({
    deliveryTickets: [],
})

const importInput = ref(null)

const isShow = ref(false)

const title = computed(() => $t('delivery_tickets.form.import'))

const show = async () => {
    isShow.value = true
}

const close = () => {
    isShow.value = false
    form.reset()
    // Reset file input
    if (importInput.value) {
        importInput.value.value = ''
    }
}

const submit = () => {
    form.post(
        $route('orders.show.delivery_tickets.import', {
            order: props.order.id,
        }),
        {
            preserveState: true,
            preserveScroll: true,
            only: ['errors', 'flash'],
            onSuccess: () => {
                emit('refresh')
                close()
            },
            onError: (errors) => emit('flash.validation', errors),
        },
    )
}

const handleImport = async (event) => {
    const { files } = event.target
    const tickets = [...files]

    tickets.forEach(async (file) => {
        const reader = new DeliveryTicketReader(file)

        const content = await reader.read()

        const types = [
            {
                name: '',
                parser: new RawTextParser(content),
            },
            {
                name: 'application/vnd.ms-excel',
                parser: new XLSParser(content),
            },
        ]

        const { parser } = types.find((type) => type.name == file.type)
        form.deliveryTickets = [...form.deliveryTickets, parser.parse()]
    })
}

defineExpose({
    show,
    close,
})
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="980px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title> {{ title }} </v-toolbar-title>
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
                <div class="tw-flex tw-justify-end tw-w-full">
                    <label
                        for="importer"
                        class="tw-text-white tw-bg-gray-500 tw-p-2 tw-rounded"
                    >
                        <span class="">Import a delivery ticket</span>
                        <input
                            id="importer"
                            ref="importInput"
                            type="file"
                            accept=".xls,xlsx,.001"
                            class="tw-hidden"
                            multiple
                            @input="handleImport"
                        />
                    </label>
                </div>
                <v-data-table
                    :headers="[
                        {
                            text: 'External Id',
                            align: 'left',
                            sortable: false,
                            value: 'externalId',
                        },
                        {
                            text: 'Address',
                            align: 'left',
                            sortable: false,
                            value: 'address',
                        },
                        {
                            text: 'Materials',
                            align: 'center',
                            sortable: false,
                            value: 'items',
                        },
                    ]"
                    :items="form.deliveryTickets"
                >
                    <template #items="props">
                        <td>{{ props.item.externalId ?? '' }}</td>
                        <td>{{ props.item.address }}</td>
                        <td class="tw-text-center">
                            {{ props.item.items.length }}
                        </td>
                    </template>
                </v-data-table>
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
                    {{ $t('buttons.import') }}
                    <template #loader>
                        <span>
                            {{
                                isCreating
                                    ? $t('buttons.creating')
                                    : $t('buttons.updating')
                            }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
