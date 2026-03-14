<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'
import OfferRow from '@Offer/Components/OfferRow.vue'
import OfferToolbar from '@Offer/Components/OfferToolbar.vue'
import { watchDebounced } from '@vueuse/core'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    offers: {
        required: false,
        type: Object,
        default: () => ({
            data: [],
            meta: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
        }),
    },
    filters: {
        required: true,
        type: Object,
    },
})

const { $t, $route } = useGlobalVariables()

const pageFilters = ref(props.filters)

const metadata = computed(() => {
    const { data, ...metadata } = props.offers
    return metadata
})
const currentPage = ref(props.offers?.meta?.current_page || 1)

watch(
    () => props.offers?.meta?.current_page,
    (newPage) => {
        if (newPage) {
            currentPage.value = newPage
        }
    },
)

const headers = [
    { text: $t('labels.id'), sortable: false },
    { text: $t('offer.inputs.title'), sortable: false },
    { text: $t('offer.inputs.recipient'), sortable: false },
    { text: $t('offer.inputs.type'), sortable: false },
    { text: $t('offer.labels.amount'), sortable: false, align: 'right' },
    { text: $t('labels.status'), sortable: false },
    { text: $t('labels.actions'), sortable: false, align: 'right' },
]

watchDebounced(
    () => pageFilters.value,
    (newFilters) => {
        fetch(1, newFilters)
    },
    { debounce: 500, deep: true },
)

function fetch(page, filters, perPage = 15) {
    router.reload({
        data: {
            ...filters,
            page,
            perPage,
        },
        preserveState: true,
        preserveScroll: true,
        only: ['offers', 'filters'],
    })
}

// function confirmDeleting(offer) {
//     $confirm({
//         title: $t('labels.delete'),
//         question: $t('offer.messages.deleting'),
//         type: 'delete',
//         confirm: () => {
//             router.delete(
//                 $route('offers.destroy', {
//                     offer,
//                 }),
//                 {
//                     preserveState: true,
//                     preserveScroll: true,
//                 },
//             )
//         },
//     })
// }
</script>
<template>
    <div class="tw-h-full">
        <v-container
            fluid
            pt-2
            grid-list-md
        >
            <v-layout
                row
                wrap
            >
                <OfferToolbar
                    :page-filters="pageFilters"
                    @update:filters="(values) => (pageFilters = values)"
                    @click:create-incoming-offer="
                        $refs.offerFormRef.show(null, 'incoming')
                    "
                    @click:create-outgoing-offer="
                        $refs.offerFormRef.show(null, 'outgoing')
                    "
                />
            </v-layout>

            <v-layout
                justify-start
                column
                fill-height
            >
                <div class="mt-2">
                    <v-subheader
                        class="!tw-px-0 md:tw-items-center tw-justify-end"
                    >
                        Showing
                        {{ offers?.data?.length ?? 0 }} of {{ metadata?.total }}
                        results.
                    </v-subheader>
                </div>

                <v-flex
                    xs12
                    class="!tw-overflow-auto"
                >
                    <v-data-table
                        :headers="headers"
                        :items="offers.data"
                        hide-actions
                        item-key="id"
                    >
                        <template #items="itemProps">
                            <OfferRow
                                :selected="itemProps.selected"
                                :offer="itemProps.item"
                                :editable="false"
                                :deletable="false"
                                @click:show-materials="
                                    itemProps.expanded = !itemProps.expanded
                                "
                                @click:show-details="
                                    (offer) =>
                                        router.get(
                                            $route('my-offers.show', {
                                                offer,
                                            }),
                                        )
                                "
                            />
                        </template>
                    </v-data-table>
                </v-flex>
                <VFlex
                    xs12
                    align-self-end
                    mt-3
                >
                    <v-pagination
                        v-model="currentPage"
                        :length="metadata?.last_page"
                        :total-visible="7"
                        @input="(page) => fetch(page, pageFilters)"
                    />
                </VFlex>
            </v-layout>
        </v-container>
    </div>
</template>
