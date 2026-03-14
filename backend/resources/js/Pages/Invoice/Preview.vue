<!-- TODO: Not used -->
<script setup>
import '@/../css/views/print-preview.css'
import { router, usePage } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import { computed, nextTick, onMounted, ref, toRef, watch } from 'vue'
import useGlobalVariables from '../../Composables/useGlobalVariables'
import { isEmpty } from '../../Composables/useUtils'
import InvoicePreviewToolbar from './Components/InvoicePreviewToolbar.vue'
import ManualItemForm from './Components/ManualItemForm.vue'
import InvoiceNotesForm from './Components/Preview/InvoiceNotesForm.vue'
import InvoiceSubjectForm from './Components/Preview/InvoiceSubjectForm.vue'
import PreviewPageSheet from './Components/Preview/PreviewPageSheet.vue'
import { isAuthenticated } from '@/Composables/useAuth'

const props = defineProps({
    invoice: {
        required: true,
        type: Object,
    },
    items: {
        required: false,
        type: Array,
        default: () => [],
    },
    permissions: {
        required: true,
        type: Object,
    },
})

const { $t, $confirm, $route } = useGlobalVariables()

const printPreviewRef = ref(null)
const invoiceNotesFormRef = ref()
const manualItemFormRef = ref(null)
const invoice = toRef(props, 'invoice')

const isDownloading = ref(false)
const isPrinting = ref(false)
const enableDownloading = ref(false)
const isOrganizingItems = ref(true)
const showForceResetButton = ref(false)

const pagesRef = ref(null)
const invoiceSubjectFormRef = ref(null)
const stashedItems = ref([])
const pageItems = ref([])
const notesPageNumber = ref(null)
const notesIsInserted = ref(false)
const logoHeight = ref(0)
const bottomRef = ref(null)

const previewLayout = computed(() => props.invoice.preview_layout)
const userIsAuthenticated = computed(() => isAuthenticated())

watch(
    isOrganizingItems,
    (newValue) => {
        showForceResetButton.value = false

        if (newValue === true) {
            setTimeout(() => {
                if (isOrganizingItems.value) {
                    showForceResetButton.value = true
                }
            }, 3000)
        }
    },
    { immediate: true },
)

onMounted(async () => {
    if (previewLayout.value) {
        pageItems.value = previewLayout.value
        const isInvoiceComplete = await isInvoiceItemsComplete()

        if (isInvoiceComplete) {
            isOrganizingItems.value = false
            enableDownloading.value = true

            await new Promise((resolve) => setTimeout(resolve, 1000))

            // Watch notesIsInserted value and trigger download when true
            const notesInsertionChecks = setInterval(() => {
                if (notesIsInserted.value) {
                    clearInterval(notesInsertionChecks)
                    if (window.location.hash === '#download-pdf') {
                        nextTick(() => {
                            download()
                        })
                    }
                }
            }, 200) // Check every 200ms
        } else {
            // Reset layout!
        }
    } else {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['items'],
            onSuccess: ({ props }) => {
                pageItems.value = [props.items]
            },
            onFinish: () => {
                window.scrollTo(0, document.body.scrollHeight)
            },
        })
    }
})

const debouncedSavePreviewLayout = useDebounceFn(async () => {
    await axios.post(
        $route('json.invoices.preview-layout.store', {
            invoice: invoice.value,
        }),
        {
            preview_layout: pageItems.value,
        },
    )
}, 500) // 500ms debounce time

async function savePreviewLayout() {
    await debouncedSavePreviewLayout()
    enableDownloading.value = true
}

async function resetPreviewLayout() {
    const user = usePage().props?.user

    if (user?.isGuest) {
        return
    }

    await axios.delete(
        $route('json.invoices.preview-layout.destroy', {
            invoice: invoice.value,
        }),
    )

    resetLineItems()
}

function resetLineItems() {
    const propsItems = props.items

    if (propsItems.length > 0) {
        pageItems.value = [propsItems]
    } else {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['items'],
            onSuccess: ({ props }) => {
                pageItems.value = [props.items]
            },
        })
    }
}

async function resetNotesPosition() {
    notesIsInserted.value = false
    notesPageNumber.value = null
    resetLineItems()
}

async function chunkInvoiceItems({ height, pageNumber }) {
    isOrganizingItems.value = true
    nextTick(async () => {
        const pageIndex = pageNumber - 1
        const maxHeight = getPageMaxHeight(pageNumber)

        // Invoice page is overflowing ... reduce items
        if (height >= maxHeight) {
            await handleOverflow(pageIndex)
            return
        }

        if (height <= maxHeight) {
            const invoiceItemsAreCompleted = await isInvoiceItemsComplete()

            if (invoiceItemsAreCompleted) {
                await insertNotes()
                return
            }

            // return
        }

        handleStashedItems()
    })
}

function getPageMaxHeight(pageNumber) {
    return pageNumber === 1 ? 1000 : 1020
}

async function insertNotes() {
    if (isEmpty(invoice.value.notes)) {
        isOrganizingItems.value = false
        notesIsInserted.value = true
        notesPageNumber.value = pageItems.value.length
        await savePreviewLayout()
        return
    }

    if (notesIsInserted.value) {
        isOrganizingItems.value = false

        await savePreviewLayout()
        return
    }

    // Try to insert notes on the last page
    const lastPage = pageItems.value.length
    notesPageNumber.value = lastPage

    nextTick(async () => {
        const pageWithNotes = await pagesRef.value[lastPage - 1].getPageHeight(
            true,
        )
        const pageMaxHeight = getPageMaxHeight(lastPage)

        if (pageWithNotes > pageMaxHeight) {
            notesIsInserted.value = true

            // Create new page and add notes from that page
            pageItems.value = [...pageItems.value, []]
            notesPageNumber.value = pageItems.value.length
        } else {
            notesIsInserted.value = true
        }
        await savePreviewLayout()
        isOrganizingItems.value = false
    })
}

async function handleOverflow(pageIndex) {
    const items = pageItems.value[pageIndex]

    if (items.length > 0) {
        const removedItem = items.pop()
        stashedItems.value = [removedItem, ...stashedItems.value]
        pageItems.value[pageIndex] = [...items]
        pageItems.value = [...pageItems.value]
    }
}

async function handleStashedItems() {
    const stashedItemsLength = stashedItems.value.length

    // Invoice previous page is not overflowing ... create new page and add removed items
    if (stashedItemsLength > 0) {
        pageItems.value = [...pageItems.value, stashedItems.value]
        stashedItems.value = []
        notesPageNumber.value = pageItems.value.length
    }
    pageItems.value = pageItems.value.filter((page) => {
        return page.length > 1
    })

    await nextTick()
    window.scrollTo(0, document.body.scrollHeight)
    bottomRef.value.scrollIntoView({ behavior: 'smooth' })
}

async function isInvoiceItemsComplete() {
    return new Promise((resolve) => {
        const invoiceItemsCount = props.items.length
        const chunkItemsTotal = pageItems.value
            .map((item) => item.length)
            .reduce((a, c) => a + c, 0)

        resolve(
            invoiceItemsCount == chunkItemsTotal ||
                invoiceItemsCount + 1 == chunkItemsTotal,
        )
    })
}

async function print() {
    isPrinting.value = true

    nextTick(async () => {
        const printContents = printPreviewRef.value.innerHTML

        let styles = Array.from(
            document.querySelectorAll('link[rel="stylesheet"], style'),
        )
            .map((node) => node.outerHTML)
            .join('')

        const printWindow = window.open('', '_blank')
        if (printWindow) {
            const printDocument = `
                <!DOCTYPE html>
                <html>
                <head>
                    ${styles}
                    <style type="text/css" media="print">
                        @page {
                            size: auto;
                            padding: 0;
                            margin: 0;
                        }
                    </style>
                </head>
                <body>
                    ${printContents}
                </body>
                </html>
            `

            printWindow.document.write(printDocument)

            // Wait for resources to load
            await new Promise((resolve) => {
                printWindow.onload = resolve
                setTimeout(resolve, 1000) // Fallback timeout
            })

            printWindow.document.close()
            printWindow.focus()

            try {
                printWindow.print()
            } catch (e) {
                console.error('Print failed:', e)
            }

            // Close window after print dialog closes
            setTimeout(() => {
                printWindow.close()
                isPrinting.value = false
            }, 1000)
        }
    })
}

function download() {
    isDownloading.value = true

    nextTick(async () => {
        const target = printPreviewRef.value // Content to be printed
        const html = target.outerHTML

        const invoice = props.invoice
        await axios
            .post($route('json.invoice.pdf.generate', { invoice }), {
                html,
            })
            .then(async ({ data }) => {
                await new Promise((resolve) => setTimeout(resolve, 1000))

                const url = data?.url
                // Open in a new tab to view the PDF
                window.open(url, '_blank')
            })
            .finally(() => {
                isDownloading.value = false
                console.log('Download process completed.')
            })
    })
}

function confirmMarkingAsPaid() {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.message.confirm_marking_as_paid'),
        type: 'warning',
        confirm: () => {
            router.post(
                window.route('invoices.mark_as_paid', {
                    invoice: invoice.value,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'flash'],
                },
            )
        },
    })
}

function confirmSendToClient() {
    $confirm({
        title: $t('labels.warning'),
        question: $t('invoices.message.confirm_send_to_client'),
        type: 'info',
        confirm: () => {
            router.post(
                window.route('invoices.send_to_client', {
                    invoice: invoice.value,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['invoice'],
                },
            )
        },
    })
}
</script>
<template>
    <div class="tw-relative tw-h-full tw-w-full">
        <ManualItemForm
            ref="manualItemFormRef"
            :invoice="invoice"
            @refresh="router.reload({ only: ['items', 'invoice'] })"
        />
        <v-dialog
            id="download-loading-dialog"
            :value="isDownloading"
            :persistent="true"
            width="300"
            content-class="download-dialog-content"
        >
            <v-card
                color="primary"
                dark
            >
                <v-card-text>
                    {{ $t('invoices.preview.downloading-invoice') }}
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    />
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog
            id="organizing-loading-dialog"
            :value="isOrganizingItems"
            :persistent="true"
            width="300"
            content-class="organizing-dialog-content"
        >
            <v-card
                color="primary"
                dark
            >
                <v-card-text>
                    {{ $t('invoices.preview.organizing-item-positions') }}
                    <v-progress-linear
                        indeterminate
                        color="white"
                        class="mb-0"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn
                        v-if="userIsAuthenticated && showForceResetButton"
                        small
                        color="secondary"
                        depressed
                        @click="
                            () => {
                                resetPreviewLayout()
                                resetLineItems()
                            }
                        "
                    >
                        Trouble? Reset Layout
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <InvoiceSubjectForm
            ref="invoiceSubjectFormRef"
            @refresh="resetLineItems"
        />

        <InvoiceNotesForm
            ref="invoiceNotesFormRef"
            :invoice="invoice"
            @refresh="resetNotesPosition"
        />

        <!-- Toolbar -->
        <InvoicePreviewToolbar
            :invoice="invoice"
            :print="print"
            :confirm-send-to-client="confirmSendToClient"
            :confirm-marking-as-paid="confirmMarkingAsPaid"
            :is-downloading="isDownloading"
            :enable-downloading="enableDownloading"
            :refresh-layout="
                () => {
                    resetPreviewLayout()
                    resetLineItems()
                }
            "
            @click:download="download"
            @click:add-manual-item="manualItemFormRef.show()"
        />

        <v-spacer class="no-print" />
        <v-divider class="no-print" />

        <v-container fluid>
            <v-flex
                id="preview-sheet-container"
                ref="printPreviewRef"
                xs12
                class="tw-justify-around tw-flex tw-flex-col"
            >
                <template v-for="(lineItems, pageIndex) in pageItems">
                    <PreviewPageSheet
                        ref="pagesRef"
                        :key="`invoice-page-${pageIndex}`"
                        :invoice="invoice"
                        :page="pageIndex + 1"
                        :items="lineItems"
                        :is-downloading="isDownloading"
                        :is-printing="isPrinting"
                        :is-first-page="pageIndex + 1 === 1"
                        :is-last-page="pageIndex + 1 === pageItems.length"
                        class="preview-sheet"
                        :notes-on-page="notesPageNumber"
                        @reduce:items="(payload) => chunkInvoiceItems(payload)"
                        @click:edit-subject="
                            invoiceSubjectFormRef.show(invoice)
                        "
                        @click:edit-notes="invoiceNotesFormRef.show()"
                        @logo:loaded="
                            (payload) => (logoHeight = payload.logoHeight)
                        "
                    />
                    <!-- <div
                        :key="`page-break-${pageIndex}`"
                        class="page-break"
                    /> -->
                </template>
            </v-flex>
        </v-container>

        <div ref="bottomRef" />
    </div>
</template>
