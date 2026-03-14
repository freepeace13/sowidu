<script setup>
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { router, usePage } from '@inertiajs/vue2'
import { getCurrentInstance } from 'vue'

defineProps({
    documents: {
        required: true,
        type: Array,
    },
    canBeEdited: {
        required: true,
        type: Boolean,
    },
})

const app = getCurrentInstance()

async function attachDocument({ uuid }) {
    const invoice = usePage().props.invoice

    router.post(
        window.route('invoices.documents.store', {
            invoice,
        }),
        {
            documents: [uuid],
        },
        {
            preserveScroll: true,
            preserveState: true,
            only: ['documents'],
        },
    )
}

async function removeDocument(document) {
    const invoice = usePage().props.invoice

    app.proxy.$root.$confirm.ask({
        title: app.proxy.$root.$t('labels.delete'),
        question: app.proxy.$root.$t(
            'invoices.message.confirm_document_removing',
        ),
        type: 'delete',
        confirm: () => {
            router.delete(
                window.route('invoices.documents.destroy', {
                    invoice,
                    document,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['flash', 'errors', 'documents'],
                },
            )
        },
    })
}
</script>
<template>
    <v-layout
        row
        wrap
    >
        <v-flex
            v-for="(file, fileIndex) in documents"
            :key="fileIndex"
            lg1
            md3
            sm6
            xs12
        >
            <v-card
                flat
                tile
                hover
                class="!tw-relative"
            >
                <v-img
                    :src="file.conversions.thumbnail"
                    aspect-ratio="1"
                    class="grey lighten-2"
                />
                <div class="tw-absolute tw-top-0 tw-right-0 tw-pt-4">
                    <v-btn
                        v-if="canBeEdited"
                        v-tooltip="`${$t('buttons.download')}`"
                        fab
                        small
                        color="info"
                        class="!tw-m-0"
                        :href="
                            $route('media.files.download', {
                                media: file.uuid,
                            })
                        "
                    >
                        <v-icon
                            dark
                            small
                        >
                            download
                        </v-icon>
                    </v-btn>
                    <v-btn
                        v-if="canBeEdited"
                        v-tooltip="`${$t('buttons.remove')}`"
                        fab
                        small
                        color="error"
                        class="!tw-ml-1"
                        @click="() => removeDocument(file.document_id)"
                    >
                        <v-icon
                            dark
                            small
                        >
                            close
                        </v-icon>
                    </v-btn>
                </div>
            </v-card>
        </v-flex>
        <v-flex
            v-if="canBeEdited"
            lg1
            md3
            sm6
            xs12
        >
            <v-card
                tile
                flat
                hover
                min-height="150"
                height="100%"
                class="tw-text-center card-border-dashed tw-cursor-pointer !tw-flex tw-justify-center tw-items-center tw-flex-col"
                @click="(e) => $refs.fileAttachmentFormMenu.show(e)"
            >
                <v-icon
                    color="primary"
                    size="60"
                >
                    add
                </v-icon>
                <v-card-actions class="tw-justify-center">
                    {{ $t('invoices.form.add-documents') }}
                </v-card-actions>
            </v-card>
        </v-flex>
        <FileAttachmentFormMenu
            ref="fileAttachmentFormMenu"
            :allowed-types="$page.props.allowedTypes"
            @attach:from-file="(file) => attachDocument(file)"
            @attach:from-media="$refs.mediaDrawerRef.show()"
        />
        <MediaDrawer
            ref="mediaDrawerRef"
            right
            fixed
            width="320"
            style="z-index: 10"
            @attach="(media) => attachDocument(media)"
        />
    </v-layout>
</template>
