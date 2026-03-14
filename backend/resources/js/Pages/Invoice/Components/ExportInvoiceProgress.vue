<script setup>
import { getPageProps } from '@/Composables/useUtils'
import { usePage } from '@inertiajs/vue2'
import { isNull } from 'lodash'
import { computed, onMounted, ref } from 'vue'

const isShow = ref(false)
const expand = ref(true)
const isCompleted = ref(false)
const batchJobStarted = ref(false)
const progress = ref(0)
const url = ref(null)

const isImpersonating = computed(() => getPageProps('user.impersonating'))
const userId = computed(() => getPageProps('user.authenticator.id'))
const channelName = computed(() =>
    isImpersonating.value
        ? `App.Models.Employee.${userId.value}`
        : `App.Models.User.${userId.value}`,
)

onMounted(() => {
    const user = usePage().props?.user
    if (user?.isGuest) {
        return
    }

    console.log(
        'ExportInvoiceProgress: Setting up listeners on channel',
        channelName.value,
    )
    console.log(
        'ExportInvoiceProgress: isImpersonating =',
        isImpersonating.value,
    )
    console.log('ExportInvoiceProgress: userId =', userId.value)

    window.Echo.private(channelName.value)
        .listen('.Modules\\Invoicify\\Events\\PdfExportStarted', (data) => {
            console.log(
                'ExportInvoiceProgress: PdfExportStarted received',
                data,
            )
            isShow.value = true
            isCompleted.value = isNull(data.finishedAt)
            batchJobStarted.value = true
        })
        .listen(
            '.Modules\\Invoicify\\Events\\PdfExportProgress',
            ({ batch }) => {
                console.log(
                    'ExportInvoiceProgress: PdfExportProgress received',
                    { batch },
                )
                isShow.value = true
                isCompleted.value = isNull(batch.finishedAt)
                batchJobStarted.value = true
                progress.value = batch?.progress ?? 0
            },
        )
        .listen('.Modules\\Invoicify\\Events\\PdfExportCompleted', (data) => {
            console.log(
                'ExportInvoiceProgress: PdfExportCompleted received',
                data,
            )
            console.log('ExportInvoiceProgress: file_url =', data.file_url)
            isShow.value = true
            isCompleted.value = true
            progress.value = 100
            url.value = data.file_url
            console.log('ExportInvoiceProgress: url.value set to', url.value)
        })
})

function close() {
    isShow.value = false
    isCompleted.value = false
    batchJobStarted.value = false
    progress.value = 0
    expand.value = true
}
</script>

<template>
    <v-snackbar
        class="export-invoice-progress"
        :value="isShow"
        :timeout="0"
        auto-height
        bottom
        right
    >
        <v-card width="100%">
            <v-toolbar
                color="grey darken-4"
                dark
                card
            >
                <v-toolbar-title class="subheading">
                    Exporting Invoices
                </v-toolbar-title>

                <v-toolbar-items class="ml-auto">
                    <v-btn
                        icon
                        @click="() => (expand = !expand)"
                    >
                        <v-icon>
                            {{ expand ? 'expand_more' : 'expand_less' }}
                        </v-icon>
                    </v-btn>

                    <v-btn
                        icon
                        @click="close"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>

            <v-expand-transition>
                <div
                    v-show="expand"
                    style="overflow-y: auto; max-height: 200px"
                >
                    <v-list
                        two-line
                        class="py-0"
                    >
                        <v-list-tile avatar>
                            <v-list-tile-avatar>
                                <v-icon
                                    v-show="!isCompleted"
                                    color="primary"
                                >
                                    downloading
                                </v-icon>

                                <v-icon
                                    v-show="isCompleted"
                                    color="success darken-2"
                                >
                                    check_circle
                                </v-icon>
                            </v-list-tile-avatar>

                            <v-list-tile-content>
                                <v-list-tile-title>
                                    Exporting invoices
                                </v-list-tile-title>

                                <v-list-tile-sub-title>
                                    <v-progress-linear
                                        :value="progress"
                                        :indeterminate="progress === 0"
                                    />
                                </v-list-tile-sub-title>
                            </v-list-tile-content>

                            <v-list-tile-action>
                                <template v-if="isCompleted && url">
                                    <a
                                        :href="url"
                                        class="tw-cursor-pointer"
                                        target="_blank"
                                        @click="() => close()"
                                    >
                                        <v-icon color="success darken-2">
                                            file_download
                                        </v-icon>
                                    </a>
                                </template>
                                <template v-else-if="isCompleted && !url">
                                    <v-icon color="warning"> warning </v-icon>
                                </template>
                            </v-list-tile-action>
                        </v-list-tile>
                    </v-list>
                </div>
            </v-expand-transition>
        </v-card>
    </v-snackbar>
</template>

<style lang="scss">
.export-invoice-progress {
    .v-snack__wrapper > .v-snack__content {
        height: auto;
        padding: 0px;
        width: 430px;
    }
}
</style>
