<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { computed, nextTick, ref, watch } from 'vue'
import { authCan } from '../../../../Composables/useAuth'
import { useDateFormat } from '../../../../Composables/useDayJs'
import {
    formatText,
    isNotEmpty,
    isNotNil,
} from '../../../../Composables/useUtils'
import InvoicePreviewFooter from './InvoicePreviewFooter.vue'
import InvoicePreviewHeader from './InvoicePreviewHeader.vue'
import InvoicePreviewInformation from './InvoicePreviewInformation.vue'

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    isPrinting: {
        required: false,
        type: Boolean,
        default: false,
    },
    isDownloading: {
        required: false,
        type: Boolean,
        default: false,
    },
    page: {
        required: false,
        type: Number,
        default: 1,
    },
    isFirstPage: {
        required: false,
        type: Boolean,
        default: true,
    },
    isLastPage: {
        required: false,
        type: Boolean,
        default: false,
    },
    notesOnPage: {
        required: false,
        type: Number,
        default: 1,
    },
})

defineExpose({ getPageHeight })

const emit = defineEmits([
    'reduce:items',
    'click:edit-subject',
    'click:edit-notes',
    'logo:loaded',
    'content:ready',
])

const { $t } = useGlobalVariables()
const invoiceItemsTableRef = ref(null)
const containerRef = ref()

const viewerCanEdit = computed(() => authCan('can_update_invoice'))
const subject = computed(() => props.invoice.subject)
const description = computed(() => formatText(props.invoice.description))
const notes = computed(() => formatText(props.invoice.notes))
const isPrintingOrDownloading = computed(
    () => props.isPrinting || props.isDownloading,
)

watch(
    () => props.items,
    () => {
        nextTick(async () => {
            await new Promise((resolve) => setTimeout(resolve, 50))
            await verifyPageHeight()
        })
    },
)

function logoHasLoaded(payload) {
    emit('logo:loaded', payload)
    verifyPageHeight(payload)
}

function getPageHeight(includeNotes = false) {
    return new Promise((resolve) => {
        resolve(
            Array.from(containerRef.value.childNodes)
                .map((node) => {
                    if (node.className === 'invoice-notes' && !includeNotes) {
                        return 0
                    }

                    return node?.offsetHeight ?? node?.clientHeight ?? 0
                })
                .reduce((sum, height) => sum + height, 0),
        )
    })
}

async function verifyPageHeight() {
    await new Promise((resolve) => setTimeout(resolve, 250))

    nextTick(async () => {
        const totalHeight = await getPageHeight()

        if (totalHeight) {
            emit('reduce:items', {
                height: totalHeight,
                pageNumber: props.page,
            })
        }
    })
}

const headers = [
    {
        text: $t('invoices.preview.table.pos'),
        value: 'name',
        sortable: false,
        align: 'left',
        width: '6%',
    },
    {
        text: $t('invoices.preview.table.item-count'),
        value: 'quantity',
        sortable: false,
        align: 'left',
        width: '7%',
    },
    {
        text: $t('invoices.preview.table.item-unit'),
        value: 'unit_name',
        sortable: false,
        align: 'left',
        width: '12%',
    },
    {
        text: $t('invoices.preview.table.item-description'),
        value: 'quantity',
        sortable: false,
        align: 'left',
    },
    {
        text: $t('invoices.preview.table.price-per-piece'),
        sortable: false,
        align: 'center',
        width: '15%',
    },
    {
        text: $t('invoices.preview.table.total-price'),
        sortable: false,
        align: 'right',
        width: '15%',
    },
]
</script>
<template>
    <v-sheet
        color="white"
        elevation="10"
        :class="[
            'preview-sheet',
            {
                'first-page': isFirstPage, // 1st page
                'subsequent-page': !isFirstPage,
                'is-printing': isPrinting,
                'is-downloading': isDownloading,
            },
        ]"
    >
        <div
            v-show="isPrintingOrDownloading"
            class="page-label"
        >
            {{
                $t('invoices.preview.page-number', {
                    page,
                })
            }}
        </div>
        <v-btn
            v-if="viewerCanEdit && !isPrintingOrDownloading && isFirstPage"
            v-tooltip="`${$t('invoices.preview.update-subject-message')}`"
            absolute
            right
            fab
            color="pink"
            class="edit-subject-button no-print"
            @click="$emit('click:edit-subject')"
        >
            <v-icon color="white">
                {{ isNotEmpty(subject) ? 'edit' : 'add' }}
            </v-icon>
        </v-btn>
        <v-container
            ref="containerRef"
            grid-list-md
            fluid
            pa-0
            class="tw-relative tw-h-full tw-flex tw-flex-col"
        >
            <InvoicePreviewHeader
                v-bind="$props"
                :show-client-and-customer-details="isFirstPage"
                @logo:loaded="(payload) => logoHasLoaded(payload)"
            />

            <!-- End Vendor Details -->

            <!-- Invoice Details -->
            <InvoicePreviewInformation
                v-show="isFirstPage"
                v-bind="$props"
            />
            <!-- End Invoice Details -->

            <!-- Subject and Description -->
            <div
                v-if="isFirstPage"
                class="tw-w-full subject-and-description"
            >
                <div class="subject-and-description-container">
                    <div
                        v-if="isNotEmpty(subject)"
                        class="mt-2 subject"
                    >
                        {{ subject }}
                    </div>
                    <!-- eslint-disable vue/no-v-html -->
                    <div
                        v-if="isNotEmpty(description)"
                        class="tw-text-[12px] tw-whitespace-normal tw-min-h-5 description"
                        v-html="description"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                </div>
            </div>

            <!-- End of Subject and Description -->

            <!-- Invoice Items -->
            <div class="invoice-body mt-2">
                <div
                    ref="invoiceItemsTableRef"
                    class="elevation-0 px-0 py-0 dense-header columns-center invoice-items-table tw-flex-1"
                >
                    <div class="v-table__overflow">
                        <table class="w-full line-items-table">
                            <thead>
                                <tr
                                    v-if="items.length && !items.at(0).blank"
                                    class="tw-h-12"
                                >
                                    <th
                                        v-for="(header, index) in headers"
                                        :key="`header-${index}`"
                                        :class="`tw-text-[12px] column !tw-font-bold !tw-text-black item-headers tw-text-${header.align}`"
                                        :width="header.width || null"
                                    >
                                        {{ header.text }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(item, itemIdx) in items">
                                    <!-- Invoice item -->
                                    <tr
                                        v-if="!item?.is_summary"
                                        :key="`line-item-${item.id}`"
                                        class="item-row no-border tw-align-top"
                                    >
                                        <td>
                                            {{ item?.line_item_number ?? '' }}
                                        </td>
                                        <td>
                                            {{ item.quantity }}
                                        </td>
                                        <td class="tw-uppercase">
                                            {{ item?.unit_name ?? '--' }}
                                        </td>
                                        <td
                                            class="tw-text-sm tw-text-[13px] text-emphasis"
                                        >
                                            <span v-if="item.is_work_log">
                                                {{ item?.name }}
                                                <br />
                                            </span>
                                            <span v-else>
                                                {{ item?.name ?? '--' }}</span
                                            >
                                        </td>
                                        <td
                                            class="tw-text-right tw-text-[13px] text-emphasis"
                                        >
                                            <span class="tw-mr-6">{{
                                                item.price_formatted
                                            }}</span>
                                        </td>
                                        <td
                                            class="tw-text-right tw-text-[13px] text-emphasis"
                                        >
                                            {{ item.subtotal_formatted }}
                                        </td>
                                    </tr>
                                    <!-- End of Invoice item -->

                                    <!-- Invoice Totals / Summaries -->
                                    <template v-if="item?.is_summary">
                                        <tr
                                            v-if="
                                                item.with_border_top &&
                                                item?.blank
                                            "
                                            :key="`${itemIdx}-with-border-top-${item.id}`"
                                        >
                                            <td colspan="4" />
                                            <td
                                                colspan="2"
                                                class="single-border"
                                            />
                                        </tr>
                                        <tr
                                            v-else-if="
                                                item.double_underline &&
                                                item?.blank
                                            "
                                            :key="`${itemIdx}-blank-underlines-${item.id}`"
                                        >
                                            <td colspan="4" />
                                            <td
                                                colspan="2"
                                                class="double-underline"
                                            />
                                            <!-- py-2 -->
                                        </tr>
                                        <tr
                                            v-else-if="item.blank"
                                            :key="`${itemIdx}-blank-${item.id}`"
                                        >
                                            <td
                                                :colspan="headers.length"
                                                :class="[
                                                    'blank',
                                                    item?.class ?? 'tw-h-5',
                                                ]"
                                            />
                                        </tr>

                                        <tr
                                            v-else
                                            :key="`${itemIdx}-summary-${item.id}`"
                                            class="summary-row"
                                        >
                                            <td :colspan="item?.colspan ?? 4" />
                                            <td
                                                class="tw-text-sm tw-text-center"
                                                :colspan="
                                                    item?.label_colspan ?? 1
                                                "
                                            >
                                                <div
                                                    v-if="item?.prefix"
                                                    class="tw-flex tw-items-center tw-justify-end"
                                                >
                                                    <div class="">
                                                        {{ item?.prefix }}
                                                    </div>
                                                    <div
                                                        class="tw-text-right tw-w-[125px] width-125"
                                                    >
                                                        {{ item?.label }}:
                                                    </div>
                                                </div>

                                                <div
                                                    v-else
                                                    :class="[
                                                        'tw-text-right',
                                                        {
                                                            'tw-font-semibold':
                                                                item?.bold_label,
                                                        },
                                                    ]"
                                                >
                                                    {{ item?.label }}:
                                                </div>
                                            </td>
                                            <td class="tw-w-[125px] width-125">
                                                <div
                                                    :class="[
                                                        'tw-text-right',
                                                        'tw-text-[13px] text-emphasis',
                                                        {
                                                            'tw-font-semibold':
                                                                item?.bold_amount,
                                                        },
                                                    ]"
                                                >
                                                    {{ item?.amount_prefix }}
                                                    {{ item?.amount_formatted }}
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End of Invoice Items list table -->
            </div>

            <div
                class="invoice-notes-and-footer-container tw-absolute tw-bottom-0 tw-left-0 tw-right-0"
            >
                <!-- Notes / Payment Date -->
                <div
                    v-if="isLastPage && notesOnPage === page"
                    class="invoice-notes"
                >
                    <div
                        v-if="isNotNil(invoice.payment_date)"
                        class="my-2 tw-flex tw-gap-x-4 tw-text-sm payment-date"
                    >
                        <div class="tw-font-semibold">
                            {{ $t('invoices.labels.payment-date') }}:
                        </div>
                        <div>
                            {{ useDateFormat(invoice.payment_date) }}
                        </div>
                    </div>
                    <div
                        v-if="invoice?.total_wage > 0"
                        class="mb-2 tw-flex tw-gap-x-4 tw-text-sm total-wage"
                    >
                        <div class="tw-font-semibold">
                            {{ $t('invoices.labels.total-wage') }}:
                        </div>
                        <div>
                            {{ invoice?.total_wage_formatted ?? '--' }}
                        </div>
                    </div>
                    <!-- eslint-disable vue/no-v-html -->
                    <div
                        v-if="isNotEmpty(notes)"
                        class="tw-text-[12px] tw-whitespace-normal tw-min-h-5 notes"
                        v-html="notes"
                    />
                    <!-- eslint-enable -->
                    <v-btn
                        v-if="viewerCanEdit && !isPrintingOrDownloading"
                        v-tooltip="`${$t('invoices.preview.update-notes')}`"
                        absolute
                        right
                        fab
                        color="info"
                        class="edit-notes-button no-print"
                        @click="$emit('click:edit-notes')"
                    >
                        <v-icon color="white">
                            {{ isNotEmpty(notes) ? 'edit' : 'add' }}
                        </v-icon>
                    </v-btn>
                </div>
                <!-- End Notes/Payment Date -->

                <!-- Company Invoice Details  -->
                <div class="invoice-footer-container tw-mt-auto">
                    <InvoicePreviewFooter v-bind="$props" />
                </div>
            </div>
        </v-container>
    </v-sheet>
</template>
