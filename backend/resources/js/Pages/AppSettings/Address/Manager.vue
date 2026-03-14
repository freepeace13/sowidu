<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router, useForm } from '@inertiajs/vue2'
import { watchDebounced } from '@vueuse/shared'
import { ref } from 'vue'
import AddressForm from './Components/AddressForm.vue'

const props = defineProps({
    addresses: {
        required: true,
        type: Array,
    },

    filters: {
        required: true,
        type: [Object, Array],
        default: () => ({}),
    },

    pagination: {
        required: true,
        type: Object,
    },
})

const { $t, $route } = useGlobalVariables()

const addressForm = ref(null)
const headers = [
    { text: $t('headings.address'), sortable: false },
    { text: $t('labels.actions'), sortable: false, align: 'end' },
]

const filter = useForm({
    q: props.filters?.q,
})

watchDebounced(
    () => filter.isDirty,
    (isDirty) => {
        if (isDirty) {
            fetch(1)
        }
    },
    { debounce: 500 },
)

function fetch(page = 1) {
    filter
        .transform((data) => ({
            ...data,
            page,
        }))
        .get($route('app.settings.addresses.manage'), {
            preserveScroll: true,
            preserveState: true,
        })
}
</script>
<template>
    <v-container
        fluid
        grid-list-md
    >
        <v-layout
            row
            wrap
            align-center
            justify-space-between
        >
            <v-flex
                xs12
                class="headline shrink font-weight-bold mb-3"
            >
                <div class="tw-flex tw-items-center">
                    <v-btn
                        flat
                        icon
                        @click="router.get($route('app.settings.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    <div>
                        {{ $t('app_settings.labels.address-manager') }}
                    </div>
                </div>
                <v-divider />
            </v-flex>
            <v-flex xs10>
                <div class="tw-flex tw-justify-between">
                    <v-text-field
                        v-model="filter.q"
                        hide-details
                        :placeholder="$t('labels.search')"
                        color="primary"
                        solo
                        flat
                        clearable
                        prepend-inner-icon="search"
                        :loading="filter.processing"
                    />
                    <!-- @click:clear="() => (filter.q = '')" -->
                </div>
            </v-flex>
            <v-flex
                xs2
                class="tw-justify-end tw-flex"
            >
                <v-btn
                    large
                    color="primary"
                    @click="addressForm.show()"
                >
                    <v-icon class="mr-1">add</v-icon>
                    {{ $t('app_settings.address.buttons.add') }}
                </v-btn>
            </v-flex>
            <v-flex
                xs12
                class="tw-text-right tw-self-end"
            >
                {{
                    $t('pagination.showing-results', {
                        partialCount: pagination.count,
                        total: pagination.total,
                    })
                }}
            </v-flex>
        </v-layout>
        <v-layout
            align-start
            column
        >
            <v-flex
                xs12
                grow
                w-full
                mt-2
            >
                <v-data-table
                    :headers="headers"
                    :headers-length="headers.length"
                    hide-actions
                    :items="addresses"
                    :loading="filter.processing"
                    class="elevation-1"
                >
                    <template #items="{ item: address }">
                        <td>{{ address.full }}</td>
                        <td class="tw-text-center">
                            <v-btn
                                flat
                                icon
                                fab
                                color="info"
                                class="x-small"
                                @click="addressForm.show(address)"
                            >
                                <v-icon
                                    dark
                                    small
                                >
                                    edit
                                </v-icon>
                            </v-btn>
                        </td>
                    </template>
                </v-data-table>
            </v-flex>
            <VFlex
                xs12
                w-full
                mt-3
                class="!tw-text-center"
            >
                <v-pagination
                    :value="pagination.current_page"
                    :length="pagination.total_page"
                    @input="(page) => fetch(page)"
                />
            </VFlex>
        </v-layout>
        <AddressForm ref="addressForm" />
    </v-container>
</template>
