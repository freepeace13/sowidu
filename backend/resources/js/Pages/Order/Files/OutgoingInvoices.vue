<template>
    <div class="fill-height tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                id="dropdown-example"
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="'Go to order details'"
                    icon
                    class="hidden-xs-only"
                    @click="$inertia.get($route('orders.show', { order }))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('order.labels.outgoing-invoices') }}
                </v-toolbar-title>

                <v-spacer />
            </v-toolbar>
        </portal>
        <JumboUploadButton
            v-if="!invoices.length"
            :title="$t('order.labels.add-outgoing-invoice')"
            @click:card="(e) => $refs.fileAttachmentFormMenu.show(e)"
        />
        <v-container
            v-else
            grid-list-lg
            text-xs-center
        >
            <v-layout
                row
                wrap
            >
                <v-flex
                    v-for="media in invoices"
                    :key="media.uuid"
                    xs4
                >
                    <FileItem
                        :id="`media_file_${media.id}`"
                        :media-id="media.id"
                        :type="media.file_type"
                        :thumbnail="media.conversions.thumbnail"
                        :name="media.file_name"
                        :tag-address="media?.address_tag?.full"
                        :uploaded-by="media.owner?.name"
                        :uploaded-at="media.created_at | toDateTimeLocal"
                        :last-modified="media.custom_properties.lastModified"
                        :category="media.category"
                        @preview="viewDocument(media)"
                        @rename="renameFile(media)"
                        @click:more="(e) => $refs.fileMenuList.show(e, media)"
                    />
                </v-flex>
            </v-layout>
        </v-container>

        <v-btn
            color="primary"
            dark
            fixed
            bottom
            right
            icon
            large
            @click="(e) => $refs.fileAttachmentFormMenu.show(e)"
        >
            <v-icon>add</v-icon>
        </v-btn>

        <FileAttachmentFormMenu
            ref="fileAttachmentFormMenu"
            @attach:from-file="(file) => attachInvoice(file)"
            @attach:from-media="$refs.mediaDrawerRef.show()"
        />

        <MediaDrawer
            ref="mediaDrawerRef"
            right
            absolute
            width="320"
            style="z-index: 10"
            @attach="(invoice) => attachInvoice(invoice)"
        />

        <MediaView ref="mediaViewer" />

        <FileMenuList
            ref="fileMenuList"
            :options="[
                'open',
                'tag-to-address',
                'tag-to-category',
                'rename',
                'download',
                'detach-to-order',
                'remove',
                'share',
                'share-to-opposite',
            ]"
            @click:open="(media) => viewMedia(media)"
            @click:rename="(media) => renameFile(media)"
            @click:share="(media) => $refs.shareMedia.start(media)"
            @click:remove="(media) => removeFile(media)"
            @click:detach-to-order="(media) => detachToOrder(media)"
            @click:download="(media) => downloadFile(media)"
            @click:details="(media) => $refs.details.start(media)"
            @click:send-to="(media) => $refs.sendFileForm.show(media)"
            @click:tag-to-address="
                (media) =>
                    $refs.tagAddressForm.show({
                        uuid: media.uuid,
                        type: 'media',
                    })
            "
            @click:share-to-opposite="
                (media) => confirmSharingToOppositeParty(media)
            "
            @click:tag-to-category="
                (media) => $refs.tagCategoryForm.show(media)
            "
        />

        <TagAddressForm
            ref="tagAddressForm"
            @refresh-media="refresh"
        />

        <MediaTagCategory
            ref="tagCategoryForm"
            :categories="categories"
            @refresh-media="refresh"
        />

        <SendFileForm
            ref="sendFileForm"
            title="Send to"
        />

        <RenameMediaForm
            ref="renameMedia"
            @success="renameFileCompleted"
        />
        <ShareFileForm
            ref="shareMedia"
            :permission-types="permissionTypes"
        />
    </div>
</template>
<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import JumboUploadButton from './Components/JumboUploadButton.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { socketIdHeader } from '@/Composables/useSocketId'
import FileItem from '@/Features/Media/Components/FileItem.vue'
import MediaView from '@/Components/Viewer/MediaView.vue'
import FileMenuList from '@/Pages/Media/Partials/FileMenuList.vue'
import TagAddressForm from '@/Apps/Media/Components/TagAddressForm.vue'
import MediaTagCategory from '@/Apps/Media/Components/MediaTagCategory.vue'
import SendFileForm from '@/Apps/Media/Components/SendFileForm.vue'
import RenameMediaForm from '@/Apps/Media/Components/RenameMediaForm.vue'
import ShareFileForm from '@/Components/Forms/ShareFileForm.vue'
import OrderListenerMixin from '../Mixins/OrderListenerMixin'

export default {
    components: {
        JumboUploadButton,
        FileAttachmentFormMenu,
        MediaView,
        MediaDrawer,
        FileItem,
        FileMenuList,
        TagAddressForm,
        MediaTagCategory,
        SendFileForm,
        RenameMediaForm,
        ShareFileForm,
    },
    mixins: [OrderListenerMixin],
    layout: [AuthLayout, OrderLayout],
    props: {
        order: {
            required: true,
            type: Object,
        },
        invoices: {
            required: true,
            type: Array,
            default: () => [],
        },
        allowedTypes: {
            required: true,
            type: Array,
        },
        categories: {
            required: false,
            type: Array,
            default: () => [],
        },
        permissionTypes: {
            required: true,
            type: Object,
        },
    },

    mounted() {
        this.$inertia.reload({ only: ['categories'] })
    },

    methods: {
        attachInvoice({ uuid: media }) {
            const order = this.order
            this.$inertia.post(
                this.$route('orders.show.files.outgoing_invoices.store', {
                    order,
                }),
                {
                    media,
                },
                {
                    headers: {
                        Accept: 'application/json',
                        ...socketIdHeader,
                    },
                    only: ['errors', 'invoices'],
                    preserveScroll: true,
                    preserveState: true,
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Outgoing invoice has been attached to this order.',
                        )
                    },
                },
            )
        },

        viewDocument({ uuid }) {
            this.$refs.mediaViewer.view(uuid)
        },

        refresh() {
            this.$inertia.reload({
                only: ['invoices'],
                preserveState: true,
                preserveScroll: true,
            })
        },

        renameFileCompleted() {
            this.$refs.renameMedia.close()
            this.refresh()
        },

        renameFile(file) {
            this.$refs.renameMedia.start(file.uuid)
        },

        removeFile(media) {
            const order = this.order

            this.$confirm.ask({
                title: 'Delete',
                question: 'Do you want to delete this outgoing invoice?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route(
                            'orders.show.files.outgoing_invoices.destroy',
                            {
                                order,
                                media: media.uuid,
                            },
                        ),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['invoices', 'errors'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Outgoing invoice has been deleted and detached.',
                                )
                            },
                        },
                    )
                },
            })
        },

        detachToOrder(media) {
            const order = this.order

            this.$confirm.ask({
                title: 'Detach',
                question:
                    'Do you want to detach this outgoing invoice from this order?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route(
                            'orders.show.files.outgoing_invoices.detach',
                            {
                                order,
                                media: media.uuid,
                            },
                        ),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            only: ['invoices', 'errors'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'Outgoing invoice has been detached to this order.',
                                )
                            },
                        },
                    )
                },
            })
        },

        confirmSharingToOppositeParty({ uuid: media }) {
            const order = this.order

            this.$confirm.ask({
                title: 'Share',
                question: this.$t(
                    'order.labels.confirm-sharing-to-opposite-party',
                ),
                type: 'warning',
                confirm: () => {
                    this.$inertia.post(
                        this.$route(
                            'orders.orders.files.share_to_opposite_party',
                            {
                                order,
                                media,
                            },
                        ),
                        {},
                        {
                            only: ['medias'],
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    'File has been shared to the opposite party.',
                                )
                            },
                            onError: (errors) => {
                                this.$root.$emit('flash.validation', errors)
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
